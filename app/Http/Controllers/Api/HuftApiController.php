<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DomainDecentralize;
use App\Models\MicrositeLink;
use App\Models\MicrositeLinkButton;
use App\Models\MicrositeLinkButtonTraffict;
use App\Models\MicrositeLinkTrafficts;
use App\Models\ShortLink;
use App\Models\ShortLinkTraffict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HuftApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $url)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $domain = DomainDecentralize::where('api_key', $token)->first();

        if ($domain) {
            $rawDomain = parse_url($domain->domain_url);
            $domainName = $rawDomain['host'] ?? $domain->domain_url;

            if ($domainName != $request['domain']) {
                return response()->json(['status' => 'error', 'message' => 'Invalid API key or domain']);
            }
            if ($domain->status == 'disable') {
                return response()->json(['status' => 'error', 'message' => 'Domain Has Been disabeled']);
            }

            $ip = $request->input('ip_address');
            $short = ShortLink::with('popUnders')->where('short_url', $url)->first();
            $microsite = MicrositeLink::with(['micrositePopUnder', 'buttons'])->where('short_url', $url)->first();

            if ($short) {
                return $this->handleShortLink($request, $url, $domain, $ip, $short);
            } elseif ($microsite) {
                return $this->handleMicrosite($request, $url, $domain, $ip, $microsite);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data Not Found']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Invalid API key']);
        }
    }

    protected function handleShortLink($request, $url, $domain, $ip, $short)
    {
        if ($request->session()->get('is_traffic') == 'microsite') {
            $request->session()->forget(['ip_address', 'ip_expires_at', 'is_traffic']);
        }
        $utmData = $this->trafficUtmData($request, 'short_link_trafficts');

        $traffic = ShortLinkTraffict::firstOrNew(array_merge([
            'date' => now()->toDateString(),
            'short_links_id' => $short->id,
            'domain_decentralizes_id' => $domain->id,
        ], $utmData));

        $traffic->fill($utmData);

        $traffic->visitor_day = (int) ($traffic->visitor_day ?? 0) + 1;

        if ($request->session()->get('ip_address') != $ip) {
            $request->session()->put([
                'ip_address' => $ip,
                'ip_expires_at' => now()->addHours(2),
                'is_traffic' => 'shortlink',
            ]);
            $traffic->unique_visitor_day = $this->uniqueVisitorValue('short_link_trafficts', true);
        } elseif (! $traffic->exists && empty($traffic->unique_visitor_day)) {
            $traffic->unique_visitor_day = $this->uniqueVisitorValue('short_link_trafficts', false);
        }

        $traffic->save();

        return response()->json($this->handleVerifiedIP($url, $short));
    }

    protected function handleMicrosite($request, $url, $domain, $ip, $microsite)
    {
        $utmData = $this->trafficUtmData($request, 'microsite_link_trafficts');

        $traffic = MicrositeLinkTrafficts::firstOrNew(array_merge([
            'date' => now()->toDateString(),
            'microsite_links_id' => $microsite->id,
            'domain_decentralizes_id' => $domain->id,
        ], $utmData));

        $traffic->fill($utmData);

        if ($request->session()->get('is_traffic') == 'shortlink') {
            $request->session()->forget(['ip_address', 'ip_expires_at', 'is_traffic']);
        }
        $traffic->visitor_day = (int) ($traffic->visitor_day ?? 0) + 1;

        if ($request->session()->get('ip_address') != $ip) {
            $request->session()->put([
                'ip_address' => $ip,
                'ip_expires_at' => now()->addHours(2),
                'is_traffic' => 'microsite',
            ]);
            $traffic->unique_visitor_day = $this->uniqueVisitorValue('microsite_link_trafficts', true);
        } elseif (! $traffic->exists && empty($traffic->unique_visitor_day)) {
            $traffic->unique_visitor_day = $this->uniqueVisitorValue('microsite_link_trafficts', false);
        }

        $traffic->save();

        return response()->json($this->handleVerifiedIP($url, $microsite, 'microsite'));
    }

    protected function handleVerifiedIP($url, $data, $type = 'default')
    {
        return ['status' => 'success', 'type' => $type, 'data' => $data];
    }

    private function uniqueVisitorValue(string $table, bool $isUnique)
    {
        static $columnTypes = [];

        $columnTypes[$table] ??= strtolower(
            DB::selectOne("SHOW COLUMNS FROM {$table} WHERE Field = 'unique_visitor_day'")->Type ?? ''
        );

        return str_contains($columnTypes[$table], 'char')
            || str_contains($columnTypes[$table], 'text')
            || str_contains($columnTypes[$table], 'enum')
            ? ($isUnique ? 'yes' : 'no')
            : ($isUnique ? 1 : 0);
    }

    private function trafficUtmData(Request $request, string $table): array
    {
        $utmData = [];

        foreach (['utm_campaign', 'utm_medium', 'utm_source', 'utm_content', 'utm_term'] as $column) {
            $value = $request->input($column);

            if ($value !== null && $value !== '' && Schema::hasColumn($table, $column)) {
                $utmData[$column] = substr((string) $value, 0, 255);
            }
        }

        return $utmData;
    }

    public function trackButtonClick(Request $request)
    {
        // Validate that the button_id is provided
        $request->validate([
            'button_id' => 'required|integer|exists:microsite_link_buttons,id',
        ]);
        $buttonId = $request->input('button_id');
        $button = MicrositeLinkButton::find($buttonId);
        if ($button) {
            if (! Schema::hasColumn('microsite_link_buttons', 'clicks')) {
                return response()->json(['status' => 'success', 'message' => 'Click accepted.']);
            }

            // Atomically increment the 'clicks' column to prevent race conditions
            $button->increment('clicks');

            // Catat log klik per-tanggal supaya bisa difilter berdasarkan rentang tanggal di halaman analytics
            MicrositeLinkButtonTraffict::create([
                'date' => now()->toDateString(),
                'microsite_link_buttons_id' => $button->id,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Click tracked.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Button not found.'], 404);
    }
}

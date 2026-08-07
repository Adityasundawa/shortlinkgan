<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DomainDecentralize;
use App\Models\ShortLink;
use App\Models\ShortLinkTraffict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShortlinkApiController extends Controller
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

        $domain = DomainDecentralize::where('api_key', $request['api_key'])->first();

        if (! $domain) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        if ($domain->domain_url != $request['domain']) {
            return response()->json(['error' => 'Invalid API key or domain'], 401);
        }

        if ($domain->status == 'disable') {
            return response()->json(['error' => 'Upps,Domain has be Disabled'], 401);
        }

        $ip = $request->input('ip_address');
        $short = ShortLink::where('short_url', $url)->first();

        if (! $short) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $traffic = ShortLinkTraffict::firstOrNew([
            'date' => now()->toDateString(),
            'short_links_id' => $short->id,
            'domain_decentralizes_id' => $domain->id,
        ]);

        $traffic->visitor_day = (int) ($traffic->visitor_day ?? 0) + 1;

        if ($request->session()->get('ip_address') != $ip) {
            $request->session()->put([
                'ip_address' => $ip,
                'ip_expires_at' => now()->addHours(2),
            ]);
            $traffic->unique_visitor_day = $this->uniqueVisitorValue('short_link_trafficts', true);
        } elseif (! $traffic->exists && empty($traffic->unique_visitor_day)) {
            $traffic->unique_visitor_day = $this->uniqueVisitorValue('short_link_trafficts', false);
        }
        $traffic->save();
        $response = $this->handleVerifiedIP($url);

        return response()->json($response);
    }

    protected function handleVerifiedIP($url)
    {
        $short = ShortLink::where('short_url', $url)->first();
        if ($short) {
            return ['message' => 'success', 'data' => $short];
        } else {
            return ['message' => 'error', 'data' => null, 'error_message' => 'Data not found'];
        }
    }

    private function uniqueVisitorValue(string $table, bool $isUnique): string|int
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

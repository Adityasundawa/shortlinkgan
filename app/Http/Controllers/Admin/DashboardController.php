<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UrlShort;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\DomainDecentralize;
use App\Models\Label;
use App\Models\LabelShortlink;
use App\Models\MicrositeLink;
use App\Models\MicrositeLinkTrafficts;
use App\Models\ShortLink;
use App\Models\ShortLinksPopUnder;
use App\Models\ShortLinkTraffict;
use App\Models\Team; // Pastikan alias Image telah ditambahkan
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // <-- Import Storage facade
use Image;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Mendapatkan ID pengguna yang sedang login
        $userId = Auth::user()->id;

        // Data yang sudah ada
        $data['labels'] = Team::find(Auth::user()->id_team);
        $data['domains'] = DomainDecentralize::get();

        if (Auth::user()->team) {
            $data['tags'] = [Auth::user()->team->team_label, Auth::user()->user_label];
        } else {
            $data['tags'] = [Auth::user()->user_label];
        }

        // --- DATA ANALYTIC BARU (DIFILTER BERDASARKAN USER) ---

        // 1. Statistik Kartu KPI (Key Performance Indicator)

        // Ambil ID Microsite yang dimiliki user
        $micrositeIds = MicrositeLink::where('users_id', $userId)->pluck('id');
        // Ambil ID Shortlink yang dimiliki user
        $shortlinkIds = ShortLink::where('user_id', $userId)->pluck('id');

        $data['totalMicrositeVisitors'] = MicrositeLinkTrafficts::whereIn('microsite_links_id', $micrositeIds)->sum('visitor_day');
        $data['totalMicrositeUnique'] = Schema::hasColumn('microsite_link_trafficts', 'fingerprint_id')
            ? MicrositeLinkTrafficts::whereIn('microsite_links_id', $micrositeIds)
                ->whereNotNull('fingerprint_id')
                ->distinct('fingerprint_id')
                ->count('fingerprint_id')
            : MicrositeLinkTrafficts::whereIn('microsite_links_id', $micrositeIds)->sum('unique_visitor_day');
        $data['totalShortlinkVisitors'] = ShortLinkTraffict::whereIn('short_links_id', $shortlinkIds)->sum('visitor_day');
        $data['totalShortlinkUnique'] = Schema::hasColumn('short_link_trafficts', 'fingerprint_id')
            ? ShortLinkTraffict::whereIn('short_links_id', $shortlinkIds)
                ->whereNotNull('fingerprint_id')
                ->distinct('fingerprint_id')
                ->count('fingerprint_id')
            : ShortLinkTraffict::whereIn('short_links_id', $shortlinkIds)->sum('unique_visitor_day');

        // 2. Top 5 Microsites (DIFILTER)
        $data['topMicrosites'] = MicrositeLink::where('users_id', $userId) // Filter MicrositeLink milik user
            ->join('microsite_link_trafficts', 'microsite_links.id', '=', 'microsite_link_trafficts.microsite_links_id')
            ->select('microsite_links.title', 'microsite_links.short_url', DB::raw('SUM(microsite_link_trafficts.visitor_day) as total_visitors'))
            ->groupBy('microsite_links.id', 'microsite_links.title', 'microsite_links.short_url')
            ->orderByDesc('total_visitors')
            ->limit(5)
            ->get();

        // 3. Top 5 Shortlinks (DIFILTER)
        $data['topShortlinks'] = ShortLink::where('user_id', $userId) // Filter ShortLink milik user
            ->join('short_link_trafficts', 'short_links.id', '=', 'short_link_trafficts.short_links_id')
            ->select('short_links.campaign_name', 'short_links.short_url', DB::raw('SUM(short_link_trafficts.visitor_day) as total_visitors'))
            ->groupBy('short_links.id', 'short_links.campaign_name', 'short_links.short_url')
            ->orderByDesc('total_visitors')
            ->limit(5)
            ->get();

        // 4. Data Bagan (Chart) untuk 7 Hari Terakhir (DIFILTER)
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        // Ambil data Microsite
        $micrositeTraffic = MicrositeLinkTrafficts::whereIn('microsite_links_id', $micrositeIds) // Filter trafik berdasarkan microsite milik user
            ->select(
                DB::raw('DATE(date) as traffic_date'),
                DB::raw('SUM(visitor_day) as total_visitors')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('traffic_date')
            ->orderBy('traffic_date')
            ->pluck('total_visitors', 'traffic_date');

        // Ambil data Shortlink
        $shortlinkTraffic = ShortLinkTraffict::whereIn('short_links_id', $shortlinkIds) // Filter trafik berdasarkan shortlink milik user
            ->select(
                DB::raw('DATE(date) as traffic_date'),
                DB::raw('SUM(visitor_day) as total_visitors')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('traffic_date')
            ->orderBy('traffic_date')
            ->pluck('total_visitors', 'traffic_date');

        // Siapkan data untuk bagan
        $dates = [];
        $chartDataMicrosite = [];
        $chartDataShortlink = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $formattedDate = $startDate->copy()->addDays($i)->format('M d'); // Format: Nov 03

            $dates[] = $formattedDate;
            $chartDataMicrosite[] = $micrositeTraffic->get($date, 0);
            $chartDataShortlink[] = $shortlinkTraffic->get($date, 0);
        }

        $data['chartData'] = [
            'labels' => $dates,
            'microsite' => $chartDataMicrosite,
            'shortlink' => $chartDataShortlink,
        ];

        // --- AKHIR DATA ANALYTIC ---

        return view('admin.index', $data);
    }

    public function store(Request $request)
    {
        // --- 1. Validasi Dasar ---
        $validationRules = [
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'tags' => 'required|string|max:255',
            'images_background' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'use_play_button' => 'nullable|boolean',
        ];

        // Logika validasi conditional untuk is_multi_redirect
        if ($request->input('is_multi_redirect')) {
            $validationRules['redirect_link'] = 'required|array';
            $validationRules['redirect_link.*'] = 'required|url|max:2048';
            $validationRules['redirect_percentage'] = 'required|array';
            $validationRules['redirect_percentage.*'] = 'required|integer|min:1|max:100';

            $totalPercentage = collect($request->input('redirect_percentage'))->sum();
            if ($totalPercentage !== 100) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Total Percentage harus tepat 100%!',
                ], 422);
            }
        } else {
            $validationRules['original_url'] = 'required|url|max:2048';
        }

        $request->validate($validationRules);

        // --- Ambil data Campaign ---
        $campaign = Campaign::find($request->input('campaign_id'));

        if (! $campaign) {
            return response()->json([
                'status' => 'error',
                'message' => 'Campaign yang dipilih tidak valid.',
            ], 422);
        }

        // --- 2. Handle File Upload (images_background) ---
        $imagePath = null;
        $usePlayButton = $request->boolean('use_play_button', true);
        if ($request->hasFile('images_background')) {
            $imageFile = $request->file('images_background');

            $destinationDir = 'images_backgrounds';
            $destinationPath = public_path($destinationDir);
            $fileName = time().'_'.uniqid().'.'.$imageFile->getClientOriginalExtension();
            $fullPath = $destinationPath.'/'.$fileName;

            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $imageFile->move($destinationPath, $fileName);

            if ($usePlayButton) {
                \App\Helpers\PlayButtonOverlay::apply($fullPath);
            }

            $imagePath = $destinationDir.'/'.$fileName;
        }

        // --- 3. Tentukan Original URL & Short URL ---
        // Asumsi UrlShort::randomString() tersedia
        $shortUrl = UrlShort::randomString();
        $originalUrlValue = $request->input('original_url');
        if ($request->input('is_multi_redirect')) {
            $originalUrlValue = 'multi-redirect-link';
        }

        // --- 4. ShortLink Creation ---
        $shortLink = ShortLink::create([
            'campaign_id' => $campaign->id,
            'campaign_name' => $campaign->name,
            'description' => $request['description'],
            'original_url' => $originalUrlValue,
            'short_url' => $shortUrl,
            'user_id' => Auth::user()->id,
            'meta' => '',
            'images_background' => $imagePath,
            'use_play_button' => $usePlayButton,
            'custom_title' => $request['custom_title'] ?? null,
            'custom_description' => $request['custom_description'] ?? null,
            'type_short_links' => 'shortlink',
            'utm_campaign' => $request['utm_campaign'] ?? null,
            'utm_medium' => $request['utm_medium'] ?? null,
            'utm_source' => $request['utm_source'] ?? null,
            'utm_content' => $request['utm_content'] ?? null,
            'utm_term' => $request['utm_term'] ?? null,
        ]);

        // --- 5. Handle Multi Redirect Links ---
        if ($request->input('is_multi_redirect') && is_array($request->input('redirect_link'))) {
            $redirectLinks = $request->input('redirect_link');
            $percentages = $request->input('redirect_percentage');

            foreach ($redirectLinks as $index => $link) {
                if (isset($percentages[$index])) {
                    ShortLinksPopUnder::create([
                        'short_links_id' => $shortLink->id,
                        'url' => $link,
                        'precentage' => (int) $percentages[$index],
                        'count' => 0,
                    ]);
                }
            }
        }

        // --- 6. Handle Tags (Labels) ---
        if (! empty($request['tags'])) {
            $labels = explode(',', $request['tags']);
            foreach ($labels as $label) {
                $labelModel = Label::firstOrCreate(['label_name' => trim($label)]);
                LabelShortlink::create([
                    'users_id' => Auth::user()->id,
                    'short_links_id' => $shortLink->id,
                    'labels_id' => $labelModel->id,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shortlink berhasil dibuat!',
            'shortLink' => $shortLink,
        ]);
    }

    public function update(Request $request)
    {
        $shortLink = ShortLink::find($request['short_id']);

        if (! $shortLink || ($shortLink->user_id !== Auth::id() && ! Auth::user()->isAdmin())) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shortlink tidak ditemukan.',
            ], 404);
        }

        $existingShortLink = ShortLink::where('short_url', $request['short_url'])
            ->where('id', '!=', $shortLink->id)
            ->first();

        if ($existingShortLink) {
            return response()->json([
                'status' => 'already_taken',
                'message' => 'Shortlink Has been taken',
                'shortLink' => $existingShortLink,
            ]);
        }

        $shortLink->update([
            'short_url' => $request['short_url'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Shortlink berhasil dibuat!',
            'shortLink' => $shortLink,
        ]);
    }

    public function editMeta(int $id)
    {
        $shortlink = ShortLink::findOrFail($id);
        abort_if($shortlink->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);

        return view('shortlink.edit', ['shortlink' => $shortlink]);
    }

    public function updateMeta(int $id, Request $request)
    {
        $validated = Validator::make($request->all(), [
            'meta' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
            ]);
        }

        $shortlink = ShortLink::findOrFail($id);
        abort_if($shortlink->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);
        $shortlink->update(['meta' => $request->meta]);

        alert()->success('Berhasil', 'Meta berhasil di simpan');

        return redirect()->back();
    }
}

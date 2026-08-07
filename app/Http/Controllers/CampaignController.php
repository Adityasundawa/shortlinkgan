<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\ShortLink;
use App\Models\ShortLinkTraffict;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = Campaign::where('user_id', Auth::id())
            ->with([
                'user',
                'shortLinks' => function ($query) {
                    // Eager load relasi traffics untuk setiap short link
                    $query->with('traffics');
                },
            ])
            ->latest()
            ->get();

        // Mengembalikan view index dengan data campaigns
        return view('admin.campaign.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean', // Diterima dari checkbox (0 atau 1)
        ]);

        try {
            // 2. Tambahkan user_id dari user yang sedang login
            $campaign = Campaign::create([
                'user_id' => Auth::id(),
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            // 3. Respon untuk AJAX/Notyf
            return response()->json([
                'status' => 'success',
                'message' => 'Campaign berhasil disimpan.',
                'campaign' => $campaign,
            ], 201);

        } catch (\Exception $e) {
            // Log error
            \Log::error('Error storing campaign: '.$e->getMessage());

            // Respon error
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan campaign: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        // 1. Otorisasi
        $this->authorizeCampaign($campaign);

        // 2. Mengembalikan object Campaign dalam format JSON
        return response()->json($campaign);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $project_id) // Menggunakan nama parameter rute Anda
    {
        // 🚨 KUNCI: Ubah nama variabel untuk konsistensi internal
        $campaign = $project_id;

        // 1. Otorisasi (Menggunakan objek Campaign yang baru di-bind)
        $this->authorizeCampaign($campaign);

        // 2. Validasi Data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        try {
            // 3. Update data
            // Pastikan nama kolom di $validatedData cocok dengan $fillable di model Campaign
            $campaign->update($validatedData); // <-- Menggunakan $campaign yang benar-benar di-bind

            return response()->json([
                'status' => 'success',
                'message' => 'Campaign berhasil diperbarui.',
            ]);

        } catch (\Exception $e) {
            // ...
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        // 1. Otorisasi
        $this->authorizeCampaign($campaign);

        try {
            // 2. Hapus Campaign (short links terkait akan ikut terhapus karena cascade)
            $campaign->delete();

            // 3. Respon sukses untuk AJAX
            return response()->json([
                'status' => 'success',
                'message' => 'Campaign berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus campaign: '.$e->getMessage(),
            ], 500);
        }
    }

    public function showAnalyticView(Campaign $campaign)
    {
        // 1. Otorisasi
        $this->authorizeCampaign($campaign);

        // 2. Ambil Short Link IDs (digunakan di domain query dan analytic helper)
        $shortLinkIds = $campaign->shortLinks->pluck('id');

        // 3. Ambil data Domain Aktif
        $domains = DB::table('domain_decentralizes')
            ->whereIn('id', function ($query) use ($shortLinkIds) {
                $query->select('domain_decentralizes_id')
                    ->from('short_link_trafficts')
                    ->whereIn('short_links_id', $shortLinkIds)
                    ->distinct();
            })
            ->orderBy('domain_url')
            ->get();

        // 4. Data Analytic Awal (30 hari terakhir)
        $initialData = $this->getAggregatedData($campaign, Carbon::now()->subDays(30), Carbon::now());

        // 🚨 KUNCI PERBAIKAN: Mengirim SEMUA data analytic ke Blade 🚨
        return view('admin.campaign.analytic', [
            'campaign' => $campaign,
            'summary' => $initialData['summary'],
            'domains' => $domains,
            // Variabel Chart/Tabel awal yang digunakan oleh JavaScript saat dimuat:
            'dailyTraffic' => $initialData['dailyTraffic'],
            'trafficByCountry' => $initialData['trafficByCountry'],
            'trafficByDevice' => $initialData['trafficByDevice'],
            'trafficByCity' => $initialData['trafficByCity'],
            'topShortLinks' => $initialData['topShortLinks'],
            // Meskipun JavaScript akan segera menggantinya, Blade tidak akan error saat rendering pertama.
        ]);
    }

    /**
     * API Endpoint: Mengambil data analitik Campaign berdasarkan rentang tanggal (AJAX).
     */
    public function getAnalyticData(Request $request, Campaign $campaign)
    {
        $this->authorizeCampaign($campaign);

        $startDate = $request->input('start_date', Carbon::now()->subDays(29)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $data = $this->getAggregatedData($campaign, $startDate, $endDate);

        return response()->json($data);
    }

    /**
     * Helper Function: Mengambil dan Mengagregasi Data dari ShortLinkTraffict.
     * Mengagregasi data dari SEMUA Short Link dalam Campaign.
     */
    private function getAggregatedData(Campaign $campaign, $startDate, $endDate)
    {
        // 1. Ambil ID Short Link dari Campaign
        $shortLinkIds = $campaign->shortLinks->pluck('id');

        if ($shortLinkIds->isEmpty()) {
            // Jika tidak ada Short Link, segera return array kosong yang lengkap.
            return [
                'summary' => ['totalPageViews' => 0, 'totalVisitors' => 0, 'avgPagePerVisit' => 0, 'lastHitTime' => 'N/A'],
                'dailyTraffic' => [],
                'trafficByCountry' => [],
                'trafficByDevice' => [],
                'trafficByCity' => [],
                'topShortLinks' => [],
                'utmPerformance' => [],
            ];
        }

        // 2. Base Query (untuk efisiensi, kita clone dan tambahkan agregasi di DB)
        $baseQuery = ShortLinkTraffict::whereIn('short_links_id', $shortLinkIds)
            ->whereBetween(DB::raw('DATE(short_link_trafficts.created_at)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $visitorAggregate = Schema::hasColumn('short_link_trafficts', 'fingerprint_id')
            ? 'COUNT(DISTINCT fingerprint_id)'
            : "SUM(CASE WHEN unique_visitor_day = 'yes' THEN 1 WHEN unique_visitor_day = 'no' THEN 0 ELSE unique_visitor_day END)";
        $qualifiedVisitorAggregate = Schema::hasColumn('short_link_trafficts', 'fingerprint_id')
            ? 'COUNT(DISTINCT short_link_trafficts.fingerprint_id)'
            : "SUM(CASE WHEN short_link_trafficts.unique_visitor_day = 'yes' THEN 1 WHEN short_link_trafficts.unique_visitor_day = 'no' THEN 0 ELSE short_link_trafficts.unique_visitor_day END)";

        // Ambil data ShortLink untuk join nanti (digunakan untuk Top Short Links)
        $shortLinks = $campaign->shortLinks->keyBy('id');

        // --- QUERY UTAMA AGREGASI ---

        // Total stats untuk summary card (gunakan Aggregation DB)
        $summaryStats = (clone $baseQuery)->select(
            DB::raw('SUM(visitor_day) as total_page_views'),
            DB::raw($visitorAggregate.' as total_unique_visitors')
        )->first();

        $totalPageViews = $summaryStats->total_page_views ?? 0;
        $totalUniqueVisitors = $summaryStats->total_unique_visitors ?? 0;

        // Last Hit Time
        $lastHit = (clone $baseQuery)->orderByDesc('short_link_trafficts.created_at')->first();
        $lastHitTime = $lastHit ? $lastHit->created_at->diffForHumans() : 'N/A';
        $avgPagePerVisit = $totalUniqueVisitors > 0 ? number_format($totalPageViews / $totalUniqueVisitors, 2) : 0;

        // --- DAILY TRAFFIC (LINE CHART) ---
        $dailyTrafficDB = (clone $baseQuery)
            ->select(
                DB::raw('DATE(short_link_trafficts.created_at) as traffic_date'),
                DB::raw('SUM(visitor_day) as page_views'),
                DB::raw($visitorAggregate.' as visitors')
            )
            ->groupBy(DB::raw('DATE(short_link_trafficts.created_at)'))
            ->orderBy('traffic_date')
            ->get();

        $dailyTraffic = $dailyTrafficDB->map(function ($item) {
            return [
                'traffic_date' => Carbon::parse($item->traffic_date)->format('d M'),
                'page_views' => (int) $item->page_views,
                'visitors' => (int) $item->visitors,
            ];
        })->values();

        // --- TOP 10 SHORT LINKS TRAFFIC ---
        $topShortLinks = (clone $baseQuery)
            ->select('short_links_id',
                DB::raw($visitorAggregate.' as visitors'),
                DB::raw('SUM(visitor_day) as page_views')
            )
            ->groupBy('short_links_id')
            ->orderByDesc('visitors')
            ->limit(10)
            ->get();

        $topShortLinksFormatted = $topShortLinks->map(function ($item) use ($shortLinks) {
            $link = $shortLinks->get($item->short_links_id);
            if ($link) {
                $fullUrl = env('APP_URL').'/'.$link->short_url;

                return [
                    'short_url' => $link->short_url,
                    'full_url' => $fullUrl,
                    'description' => $link->description,
                    'original_url' => $link->original_url,
                    'id' => $link->id,
                    'visitors' => (int) $item->visitors,
                    'page_views' => (int) $item->page_views,
                ];
            }

            return null;
        })->filter()->values();

        // --- TRAFFIC BY COUNTRY, DEVICE, CITY (Aggregasi) ---

        $trafficByCountry = (clone $baseQuery)
            ->select('country', DB::raw($visitorAggregate.' as visitors'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('visitors')
            ->limit(10)
            ->get();

        $trafficByDevice = (clone $baseQuery)
            ->select('device_type', DB::raw($visitorAggregate.' as visitors'))
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->orderByDesc('visitors')
            ->get();

        $trafficByCity = (clone $baseQuery)
            ->select('city', 'country', DB::raw('SUM(visitor_day) as page_views'), DB::raw($visitorAggregate.' as visitors'))
            ->whereNotNull('city')
            ->groupBy('city', 'country')
            ->orderByDesc('visitors')
            ->limit(15)
            ->get();

        $utmPerformance = collect(['utm_campaign', 'utm_source', 'utm_medium', 'utm_content', 'utm_term'])
            ->flatMap(function ($parameter) use ($baseQuery, $qualifiedVisitorAggregate) {
                return (clone $baseQuery)
                    ->join('short_links', 'short_link_trafficts.short_links_id', '=', 'short_links.id')
                    ->whereNotNull("short_links.$parameter")
                    ->where("short_links.$parameter", '!=', '')
                    ->selectRaw('? as parameter', [$parameter])
                    ->select("short_links.$parameter as value")
                    ->selectRaw('SUM(short_link_trafficts.visitor_day) as page_views')
                    ->selectRaw($qualifiedVisitorAggregate.' as visitors')
                    ->groupBy("short_links.$parameter")
                    ->orderByDesc('visitors')
                    ->get();
            })
            ->sortByDesc('visitors')
            ->values()
            ->map(fn ($item) => [
                'parameter' => $item->parameter,
                'value' => $item->value,
                'page_views' => (int) $item->page_views,
                'visitors' => (int) $item->visitors,
            ]);

        // 3. --- RETURN FINAL ---
        return [
            'summary' => [
                'totalPageViews' => number_format($totalPageViews),
                'totalVisitors' => number_format($totalUniqueVisitors),
                'avgPagePerVisit' => $avgPagePerVisit,
                'lastHitTime' => $lastHitTime,
            ],
            'dailyTraffic' => $dailyTraffic,
            'trafficByCountry' => $trafficByCountry,
            'trafficByDevice' => $trafficByDevice,
            'trafficByCity' => $trafficByCity,
            'topShortLinks' => $topShortLinksFormatted,
            'utmPerformance' => $utmPerformance,
        ];
    }

    private function authorizeCampaign(Campaign $campaign): void
    {
        abort_if($campaign->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);
    }
}

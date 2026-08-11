<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\DomainDecentralize;
use App\Models\Label;
use App\Models\LabelShortlink;
use App\Models\ShortLink;
use App\Models\ShortLinksPopUnder;
use App\Models\ShortLinkTraffict;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage; // <-- Import Storage facade
use Illuminate\Support\Facades\Validator; // Pastikan DB facade di-import

class ProjectAnalyticController extends Controller
{
    public function list(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $perPage = min(max((int) $request->input('per_page', 25), 10), 100);

        $datasQuery = ShortLink::query()
            ->with(['user:id,name,user_label,role', 'campaign:id,name'])
            ->where('type_short_links', 'shortlink');

        if (! Auth::user()->isAdmin()) {
            $datasQuery->where('user_id', Auth::id());
        }

        if ($search !== '') {
            $datasQuery->where(function ($query) use ($search) {
                $query->where('campaign_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('original_url', 'like', "%{$search}%")
                    ->orWhere('short_url', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('user_label', 'like', "%{$search}%");
                    })
                    ->orWhereHas('campaign', function ($campaignQuery) use ($search) {
                        $campaignQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (Auth::user()->team) {
            $tags = [Auth::user()->team->team_label, Auth::user()->user_label];
        } else {
            $tags = [Auth::user()->user_label];
        }
        $datas = $datasQuery->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        $campaigns = Campaign::query()
            ->when(! Auth::user()->isAdmin(), fn ($query) => $query->where('user_id', Auth::id()))
            ->get();
        $domains = DomainDecentralize::orderBy('domain_url')->get();

        return view('admin.analytic.list', compact('domains', 'tags', 'datas', 'campaigns', 'search', 'perPage'));
    }

    public function update(Request $request)
    {
        $isMultiRedirect = $request->boolean('is_multi_redirect');

        // --- 1. Adjust Validation Keys to Match Form Names ---
        $validate = Validator::make($request->all(), [
            'edit_dataid' => 'required|integer',        // Matches HTML: id="edit_data_id", name="edit_dataid"
            'campaign_name' => 'required|string',       // Matches HTML: id="edit_campaign_name", name="campaign_name"
            'tags' => 'required|string',                // Matches HTML: id="edit_tags", name="tags"
            'original_url' => $isMultiRedirect ? 'nullable|string' : 'required|url',
            'result_link' => 'required|string|max:255', // Matches HTML: id="edit_result_link", name="result_link"
            'redirect_link' => $isMultiRedirect ? 'required|array|min:1' : 'nullable|array',
            'redirect_link.*' => $isMultiRedirect ? 'required|url|max:2048' : 'nullable|url|max:2048',
            'redirect_percentage' => $isMultiRedirect ? 'required|array|min:1' : 'nullable|array',
            'redirect_percentage.*' => $isMultiRedirect ? 'required|integer|min:1|max:100' : 'nullable|integer|min:1|max:100',

            // New field names are correct
            'images_background' => 'nullable|image|max:2048',
            'custom_title' => 'nullable|string|max:255',
            'custom_description' => 'nullable|string|max:500',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 422);
        }

        if ($isMultiRedirect && collect($request->input('redirect_percentage', []))->sum() !== 100) {
            return response()->json([
                'status' => 'error',
                'message' => 'Total percentage Multi Redirect harus tepat 100%.',
            ], 422);
        }

        // Use the correct ID from the request
        $dataId = $request->edit_dataid;

        // Find the record to update
        $shortLink = ShortLink::find($dataId);
        if (! $shortLink) {
            return response()->json([
                'status' => 'error',
                'message' => 'Short link not found.',
            ], 404);
        }

        if ($shortLink->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke short link ini.',
            ], 403);
        }

        // Check for duplicate link code, excluding the current ID
        $duplicate_code_link = ShortLink::where('id', '!=', $dataId)
            ->where('short_url', $request->result_link); // Use result_link
        if ($duplicate_code_link->count() >= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode Link sudah terpakai. Gunakan kode lain',
            ], 409);
        }

        // --- 2. Adjust Data Assignment to Match Form Names ---
        $updateData = [
            'campaign_name' => $request->campaign_name,  // Use campaign_name
            'description' => $request->description,
            'original_url' => $isMultiRedirect ? 'multi-redirect-link' : $request->original_url,
            'short_url' => $request->result_link,        // Use result_link
            'is_popunder' => $isMultiRedirect ? 'yes' : 'no',

            // UTM fields are fine
            'utm_campaign' => $request->utm_campaign,
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_content' => $request->utm_content,
            'utm_term' => $request->utm_term,

            // New fields are fine
            'custom_title' => $request->custom_title,
            'custom_description' => $request->custom_description,
        ];

        // ... di dalam controller method Anda ...

        // Tentukan direktori tujuan
        // Ini adalah folder di dalam 'public' (bisa diakses publik)
        $destinationPath = public_path('images_backgrounds');

        // --- Image Upload Logic ---
        if ($request->hasFile('images_background')) {
            $file = $request->file('images_background');

            // 1. Tentukan nama file baru yang unik
            // Gabungkan timestamp, hash random, dan ekstensi asli
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

            // 2. Hapus file lama (jika ada) menggunakan fungsi PHP native: unlink()
            if ($shortLink->images_background) {
                // Asumsi $shortLink->images_background menyimpan path yang relatif ke 'public'
                // Contoh: 'backgrounds/namafilelama.jpg'
                $oldFilePath = public_path($shortLink->images_background);

                // Cek apakah file benar-benar ada sebelum menghapus
                if (file_exists($oldFilePath)) {
                    // Hapus file
                    unlink($oldFilePath);
                }
            }

            // 3. Pastikan direktori tujuan ada, menggunakan fungsi PHP native: is_dir() dan mkdir()
            if (! is_dir($destinationPath)) {
                // Buat direktori secara rekursif dengan izin 0777
                mkdir($destinationPath, 0777, true);
            }

            // 4. Pindahkan file baru menggunakan metode move() dari objek UploadedFile
            $file->move($destinationPath, $filename);

            // 5. Simpan path yang relatif ke folder 'public' di database
            $pathForDatabase = 'images_backgrounds/'.$filename;
            $updateData['images_background'] = $pathForDatabase;
        }

        // Update the ShortLink data
        $shortLink->update($updateData);

        ShortLinksPopUnder::where('short_links_id', $dataId)->delete();
        if ($isMultiRedirect) {
            $redirectLinks = $request->input('redirect_link', []);
            $percentages = $request->input('redirect_percentage', []);

            foreach ($redirectLinks as $index => $link) {
                ShortLinksPopUnder::create([
                    'short_links_id' => $dataId,
                    'url' => $link,
                    'precentage' => $percentages[$index] ?? 0,
                    'count' => 0,
                ]);
            }
        }

        // --- Tag/Label Update Logic ---
        // Use the correct ID
        LabelShortlink::where('short_links_id', $dataId)->delete();

        // Recreate labels
        $tags = explode(',', $request->tags);
        foreach ($tags as $label) {
            $trimmedLabel = trim($label);
            if (! empty($trimmedLabel)) {
                $labelModel = Label::firstOrCreate(['label_name' => $trimmedLabel]);
                LabelShortlink::create([
                    'users_id' => Auth::id(),
                    'short_links_id' => $dataId, // Use the correct ID
                    'labels_id' => $labelModel->id,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been updated',
        ], 200);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataID' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 500);
        }
        $shortLink = ShortLink::findOrFail($request->dataID);
        abort_if($shortLink->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);

        $shortLink->delete();
        LabelShortlink::where('short_links_id', $request->dataID)->delete();

        return response()->json(['status' => 'success', 'message' => 'data has been deleted'], 200);
    }

    public function analytic($project_id)
    {
        // Ganti $detail menjadi $shortLink untuk konsistensi
        $shortLink = ShortLink::findOrFail($project_id);
        abort_if($shortLink->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);

        // Ambil domain yang aktif, diasumsikan ada relasi hasMany di model ShortLink
        // Jika tidak ada relasi, sesuaikan cara pengambilan domain yang terkait.
        // Berdasarkan kode Blade Anda sebelumnya, ini mungkin adalah DomainDecentralize yang digunakan
        // untuk ShortLink ini. Karena relasi tidak terlihat di model ShortLink,
        // saya akan asumsikan ada relasi many-to-many atau hasMany di ShortLink ke DomainDecentralize
        // atau kita ambil semua DomainDecentralize untuk ditampilkan sebagai pilihan link.

        // Untuk tujuan tampilan domain aktif seperti di Blade Anda:
        $domains = DomainDecentralize::orderBy('domain_url')->get(); // Atau disaring jika ada relasi spesifik

        // Total Summary untuk tampilan awal (seperti di gambar)
        $totalPageViews = ShortLinkTraffict::where('short_links_id', $project_id)->sum('visitor_day');
        // Total unique visitors
        $visitorAggregate = Schema::hasColumn('short_link_trafficts', 'fingerprint_id')
            ? 'COUNT(DISTINCT fingerprint_id)'
            : "SUM(CASE WHEN unique_visitor_day = 'yes' THEN 1 WHEN unique_visitor_day = 'no' THEN 0 ELSE unique_visitor_day END)";
        $totalVisitors = ShortLinkTraffict::where('short_links_id', $project_id)
            ->selectRaw($visitorAggregate.' as visitors')
            ->value('visitors') ?? 0;
        // Menghitung rata-rata halaman per kunjungan (contoh sederhana: Total Views / Total Unique Visitors)
        $avgPagePerVisit = $totalVisitors > 0 ? number_format($totalPageViews / $totalVisitors, 2) : 0;

        $lastHit = ShortLinkTraffict::where('short_links_id', $project_id)->orderBy('date', 'desc')->first();

        // Data Ringkasan untuk Blade
        $summary = [
            'totalPageViews' => number_format($totalPageViews, 0, ',', '.'),
            'totalVisitors' => number_format($totalVisitors, 0, ',', '.'),
            'avgPagePerVisit' => $avgPagePerVisit,
            'lastHitTime' => $lastHit ? Carbon::parse($lastHit->date)->format('H:i:s d F') : 'N/A',
        ];

        return view('admin.analytic.redirect', compact('shortLink', 'domains', 'summary'));
    }

    /**
     * Menyediakan data JSON untuk grafik dan tabel melalui AJAX.
     */
    public function analyticJSON(Request $request, $project_id)
    {
        // Pastikan ShortLink ada
        $shortLink = ShortLink::findOrFail($project_id);
        abort_if($shortLink->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);

        // 1. Validasi dan Ambil Rentang Tanggal
        $startDate = $request->input('start_date', Carbon::now()->subDays(29)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Query dasar
        $baseQuery = ShortLinkTraffict::where('short_links_id', $project_id)
            ->whereBetween('date', [$startDate, $endDate]);
        $visitorAggregate = Schema::hasColumn('short_link_trafficts', 'fingerprint_id')
            ? 'COUNT(DISTINCT fingerprint_id)'
            : "SUM(CASE WHEN unique_visitor_day = 'yes' THEN 1 WHEN unique_visitor_day = 'no' THEN 0 ELSE unique_visitor_day END)";

        // --- 1. Data Trafik Harian (Line Chart) ---
        $dailyTraffic = (clone $baseQuery)
            ->select(
                DB::raw('DATE(date) as traffic_date'),
                DB::raw('SUM(visitor_day) as page_views'),
                DB::raw($visitorAggregate.' as visitors')
            )
            ->groupBy('traffic_date')
            ->orderBy('traffic_date')
            ->get();

        // --- 2. Data Trafik Berdasarkan Negara (Donut/Bar Chart & Tabel) ---
        $trafficByCountry = (clone $baseQuery)
            ->select('country', DB::raw('SUM(visitor_day) as page_views'), DB::raw($visitorAggregate.' as visitors'))
            ->groupBy('country')
            ->orderByDesc('visitors')
            ->limit(10)
            ->get();

        // --- 3. Data Trafik Berdasarkan Kota (Tabel Detail) ---
        $trafficByCity = (clone $baseQuery)
            ->select('city', 'country', DB::raw('SUM(visitor_day) as page_views'), DB::raw($visitorAggregate.' as visitors'))
            // Gabungkan kota yang 'null' menjadi 'Unknown City'
            ->groupBy('city', 'country')
            ->orderByDesc('visitors')
            ->limit(15)
            ->get();

        // --- 4. Data Trafik Berdasarkan Tipe Perangkat (Donut Chart) ---
        $trafficByDevice = (clone $baseQuery)
            ->select('device_type', DB::raw('SUM(visitor_day) as page_views'), DB::raw($visitorAggregate.' as visitors'))
            ->groupBy('device_type')
            ->orderByDesc('visitors')
            ->get();

        $summaryStats = (clone $baseQuery)
            ->selectRaw('SUM(visitor_day) as page_views')
            ->selectRaw($visitorAggregate.' as visitors')
            ->first();

        $utmColumns = ['utm_campaign', 'utm_source', 'utm_medium', 'utm_content', 'utm_term'];
        $hasTrafficUtmColumns = collect($utmColumns)
            ->every(fn ($column) => Schema::hasColumn('short_link_trafficts', $column));

        $utmPerformance = $hasTrafficUtmColumns
            ? collect($utmColumns)
                ->flatMap(function ($parameter) use ($baseQuery, $visitorAggregate) {
                    return (clone $baseQuery)
                        ->whereNotNull($parameter)
                        ->where($parameter, '!=', '')
                        ->selectRaw("'{$parameter}' as parameter")
                        ->select("$parameter as value")
                        ->selectRaw('SUM(visitor_day) as page_views')
                        ->selectRaw($visitorAggregate.' as visitors')
                        ->groupBy($parameter)
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
                ])
            : collect([
                'utm_campaign' => $shortLink->utm_campaign,
                'utm_source' => $shortLink->utm_source,
                'utm_medium' => $shortLink->utm_medium,
                'utm_content' => $shortLink->utm_content,
                'utm_term' => $shortLink->utm_term,
            ])
                ->filter(fn ($value) => filled($value))
                ->map(fn ($value, $parameter) => [
                    'parameter' => $parameter,
                    'value' => $value,
                    'page_views' => (int) ($summaryStats->page_views ?? 0),
                    'visitors' => (int) ($summaryStats->visitors ?? 0),
                ])
                ->values();

        return response()->json([
            'dailyTraffic' => $dailyTraffic,
            'trafficByCountry' => $trafficByCountry,
            'trafficByCity' => $trafficByCity,
            'trafficByDevice' => $trafficByDevice,
            'utmPerformance' => $utmPerformance,
        ]);
    }
}

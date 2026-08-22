<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UrlShort;
use App\Http\Controllers\Controller;
use App\Models\DomainDecentralize;
use App\Models\MicrositeLink;
use App\Models\MicrositeLinkButton;
use App\Models\MicrositeLinkTrafficts;
use App\Models\MicrositePopUnder;
use App\Models\TemplateMicrosite;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MicrositeLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projectsQuery = MicrositeLink::query();

        if (! Auth::user()->isAdmin()) {
            $projectsQuery->where('users_id', Auth::id());
        }

        if ($request->search) {
            $projects = $projectsQuery->where(function ($query) use ($request) {
                $query->orWhereHas('user', function ($subQuery) use ($request) {
                    $subQuery->where('username', 'like', "%$request->search%")
                        ->orWhere('name', 'like', "%$request->search%");
                })
                    ->orWhere('title', 'like', "%$request->search%")
                    ->orWhere('description', 'like', "%$request->search%")
                    ->orWhere('short_url', 'like', "%$request->search%");
            })
                ->orderBy('created_at', 'desc')
                ->paginate(25);
        } else {
            $projects = $projectsQuery->orderBy('created_at', 'desc')->paginate(25);
        }

        $buttons = MicrositeLinkButton::all();
        $domains = DomainDecentralize::orderBy('domain_url')->get();

        return view('admin.microsite.index', compact('projects', 'domains'))->with([
            'search' => $request->search ?? '',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['themes'] = TemplateMicrosite::get();

        return view('admin.microsite.create', $data);
    }

    public function store_microsite(Request $request)
    {
        $shortUrl = UrlShort::randomString();
        $microsite = MicrositeLink::create([
            'images' => 'default.jpg',
            'title' => $request['micrositeName'],
            'description' => $request['micrositeDesc'],
            'meta' => 'meta',
            'users_id' => Auth::user()->id,
            'short_url' => $shortUrl,
            'template_microsites_id' => $request['themeId'],
        ]);

        return response()->json(['success' => true, 'message' => $microsite]);
    }

    public function create_microsite($id)
    {
        $data['microsite'] = MicrositeLink::find($id);

        return view('admin.microsite.create_microsite', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validate = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'photo' => 'nullable|file|image|max:1024', // max:1024 = 1MB
            'background' => 'nullable|file|image|max:1024', // max:1024 = 1MB
            'use_play_button' => 'nullable|boolean',
            'percentage.*' => 'nullable|integer|min:0|max:100',
            // Tambahkan validasi untuk URL popunder agar tidak kosong jika ada percentage
            'popunder_link.*' => 'nullable|url|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 422);
        }

        // 2. Proses Upload File (Menggunakan MOVE ke public/microsite)
        $filename = '';
        $background = '';
        $destinationPath = public_path('microsite');

        if (! file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        // --- PHOTO ---
        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            $filename = $photoFile->hashName();
            $photoFile->move($destinationPath, $filename);
        }

        // --- BACKGROUND ---
        $usePlayButton = $request->boolean('use_play_button', true);
        if ($request->hasFile('background')) {
            $backgroundFile = $request->file('background');
            $background = $backgroundFile->hashName();
            $backgroundFile->move($destinationPath, $background);

            if ($usePlayButton) {
                \App\Helpers\PlayButtonOverlay::apply($destinationPath.'/'.$background);
            }
        }

        // 3. Simpan Data Microsite (Master)
        $new_microsite = new MicrositeLink;
        $new_microsite->title = $request->title;
        $new_microsite->description = $request->description;
        $new_microsite->images = $filename;
        $new_microsite->short_url = UrlShort::randomString();
        $new_microsite->users_id = Auth::user()->id;
        $new_microsite->images_background = $background;
        $new_microsite->use_play_button = $usePlayButton;
        $new_microsite->save();

        // 4. Simpan Data Popunder (Perbaikan Error 1364)
        if ($request->popunder_link) {
            foreach ($request->popunder_link as $key => $popunder_link) {

                // Lewati jika link kosong, meskipun percentage ada.
                if (empty($popunder_link)) {
                    continue;
                }

                // Ambil nilai percentage. Gunakan 0 jika tidak ada atau bukan angka.
                $percentage_value = isset($request->percentage[$key]) ? (int) $request->percentage[$key] : 0;

                $pop_under = new MicrositePopUnder;
                $pop_under->url = $popunder_link;
                $pop_under->microsite_link_id = $new_microsite->id;

                // Menggunakan 'percentage' sesuai ejaan di query error Anda
                $pop_under->percentage = $percentage_value;
                $pop_under->precentage = $percentage_value;

                // **PERBAIKAN KRUSIAL**: Tambahkan nilai untuk 'count' yang kemungkinan besar adalah kolom NOT NULL
                // $pop_under->count = 0;

                $pop_under->save();
            }
        }

        // 5. Simpan Data Tombol
        if ($request->button_caption) {
            foreach ($request->button_caption as $key => $button_caption) {
                // Lewati jika caption atau link kosong
                if (empty($button_caption) || empty($request->button_link[$key])) {
                    continue;
                }
                $buttons = new MicrositeLinkButton;
                $buttons->title = $button_caption;
                $buttons->url = $request->button_link[$key];
                $buttons->microsite_links_id = $new_microsite->id;
                $buttons->save();
            }
        }

        // 6. Respon Sukses
        return response()->json([
            'status' => 'success',
            'message' => 'new Microsite Data has been saved',
        ], 200);
    }

    public function getDataById($id)
    {
        try {
            $data = MicrositeLink::findOrFail($id);
            $this->authorizeMicrosite($data);
            $buttons = MicrositeLinkButton::where('microsite_links_id', $data->id)->get();
            $oldImagePath = str_replace('public', 'storage', $data->images);
            $micrositepopunder = MicrositePopUnder::where('microsite_link_id', $data->id)->get();
            $data->button = $buttons;
            $data->gambar = $oldImagePath;
            $data->popunders = $micrositepopunder;

            Log::info('Data berhasil diambil:', ['data' => $data]);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error:', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to fetch data.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($project_id)
    {
        // Ganti $detail menjadi $shortLink untuk konsistensi
        $shortLink = MicrositeLink::findOrFail($project_id);
        $this->authorizeMicrosite($shortLink);

        // Ambil domain yang aktif, diasumsikan ada relasi hasMany di model ShortLink
        // Jika tidak ada relasi, sesuaikan cara pengambilan domain yang terkait.
        // Berdasarkan kode Blade Anda sebelumnya, ini mungkin adalah DomainDecentralize yang digunakan
        // untuk ShortLink ini. Karena relasi tidak terlihat di model ShortLink,
        // saya akan asumsikan ada relasi many-to-many atau hasMany di ShortLink ke DomainDecentralize
        // atau kita ambil semua DomainDecentralize untuk ditampilkan sebagai pilihan link.

        // Untuk tujuan tampilan domain aktif seperti di Blade Anda:
        $domains = DomainDecentralize::orderBy('domain_url')->get(); // Atau disaring jika ada relasi spesifik

        // Total Summary untuk tampilan awal (seperti di gambar)
        $totalPageViews = MicrositeLinkTrafficts::where('microsite_links_id', $project_id)->sum('visitor_day');
        // Total unique visitors
        $visitorAggregate = Schema::hasColumn('microsite_link_trafficts', 'fingerprint_id')
            ? 'COUNT(DISTINCT fingerprint_id)'
            : "SUM(CASE WHEN unique_visitor_day = 'yes' THEN 1 WHEN unique_visitor_day = 'no' THEN 0 ELSE unique_visitor_day END)";
        $totalVisitors = MicrositeLinkTrafficts::where('microsite_links_id', $project_id)
            ->selectRaw($visitorAggregate.' as visitors')
            ->value('visitors') ?? 0;
        // Menghitung rata-rata halaman per kunjungan (contoh sederhana: Total Views / Total Unique Visitors)
        $avgPagePerVisit = $totalVisitors > 0 ? number_format($totalPageViews / $totalVisitors, 2) : 0;

        $lastHit = MicrositeLinkTrafficts::where('microsite_links_id', $project_id)->orderBy('date', 'desc')->first();

        // Data Ringkasan untuk Blade
        $summary = [
            'totalPageViews' => number_format($totalPageViews, 0, ',', '.'),
            'totalVisitors' => number_format($totalVisitors, 0, ',', '.'),
            'avgPagePerVisit' => $avgPagePerVisit,
            'lastHitTime' => $lastHit ? Carbon::parse($lastHit->date)->format('H:i:s d F') : 'N/A',
        ];

        return view('admin.analytic.microsite', compact('shortLink', 'domains', 'summary'));
    }

    /**
     * Menyediakan data JSON untuk grafik dan tabel melalui AJAX.
     */
    public function analyticJSON(Request $request, $project_id)
    {
        // Pastikan ShortLink ada
        $shortLink = MicrositeLink::findOrFail($project_id);
        $this->authorizeMicrosite($shortLink);

        // 1. Validasi dan Ambil Rentang Tanggal
        $startDate = $request->input('start_date', Carbon::now()->subDays(29)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Query dasar
        $baseQuery = MicrositeLinkTrafficts::where('microsite_links_id', $project_id)
            ->whereBetween('date', [$startDate, $endDate]);
        $visitorAggregate = Schema::hasColumn('microsite_link_trafficts', 'fingerprint_id')
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

        $utmColumns = ['utm_campaign', 'utm_source', 'utm_medium', 'utm_content', 'utm_term'];
        $hasTrafficUtmColumns = collect($utmColumns)
            ->every(fn ($column) => Schema::hasColumn('microsite_link_trafficts', $column));

        $utmPerformance = $hasTrafficUtmColumns
            ? collect($utmColumns)
                ->flatMap(function ($parameter) use ($baseQuery, $visitorAggregate) {
                    return (clone $baseQuery)
                        ->whereNotNull($parameter)
                        ->where($parameter, '!=', '')
                        ->select("$parameter as value")
                        ->selectRaw('SUM(visitor_day) as page_views')
                        ->selectRaw($visitorAggregate.' as visitors')
                        ->groupBy($parameter)
                        ->orderByDesc('visitors')
                        ->get()
                        ->map(fn ($item) => [
                            'parameter' => $parameter,
                            'value' => $item->value,
                            'page_views' => (int) $item->page_views,
                            'visitors' => (int) $item->visitors,
                        ]);
                })
                ->sortByDesc('visitors')
                ->values()
            : collect();

        // Klik tombol difilter sesuai rentang tanggal yang dipilih, menggunakan log klik
        // per-tanggal (microsite_link_button_trafficts). Kolom `clicks` di microsite_link_buttons
        // sendiri adalah counter total seumur hidup dan sengaja tidak dipakai di sini karena
        // tidak menyimpan tanggal kejadian sehingga tidak bisa difilter.
        $buttonClicksBaseQuery = MicrositeLinkButton::where('microsite_links_id', $project_id)
            ->select('id', 'title', 'url');

        if (Schema::hasTable('microsite_link_button_trafficts')) {
            $buttonClicksBaseQuery->withCount(['clickTrafficts as clicks' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
            }])->orderByDesc('clicks');
        } else {
            // Fallback untuk environment yang belum menjalankan migration terbaru:
            // tampilkan total klik seumur hidup (tidak benar-benar difilter tanggal).
            $hasButtonClicksColumn = Schema::hasColumn('microsite_link_buttons', 'clicks');
            $buttonClicksBaseQuery->selectRaw($hasButtonClicksColumn ? 'clicks' : '0 as clicks');

            if ($hasButtonClicksColumn) {
                $buttonClicksBaseQuery->orderByDesc('clicks');
            }
        }

        $buttonClicks = $buttonClicksBaseQuery
            ->get()
            ->map(fn ($button) => [
                'id' => $button->id,
                'title' => $button->title,
                'url' => $button->url,
                'clicks' => (int) ($button->clicks ?? 0),
            ]);

        return response()->json([
            'dailyTraffic' => $dailyTraffic,
            'trafficByCountry' => $trafficByCountry,
            'trafficByCity' => $trafficByCity,
            'trafficByDevice' => $trafficByDevice,
            'utmPerformance' => $utmPerformance,
            'buttonClicks' => $buttonClicks,
        ]);
    }

    public function update(Request $request)
    {
        try {
            // return $request;
            $validate = Validator::make($request->all(), [
                'edit_dataid' => 'required|integer',
                'microsite_name' => 'required|string',
                'images' => 'file|image|max:1024',
                'result_link' => 'required|string',
                'button-title.*' => 'required|string',
                'button-url.*' => 'required|string',
                'popunder_link.*' => 'required|string',
                'percentage.*' => 'required',
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fill in the data completely or the image size is too large',
                ], 500);
            }

            $id = $request['edit_dataid'];

            $data = MicrositeLink::findOrFail($id);
            $this->authorizeMicrosite($data);
            $popunder = MicrositePopUnder::where('microsite_link_id', $data->id)->get();
            $button = MicrositeLinkButton::where('microsite_links_id', $data->id)->get();

            if ($request->button_caption) {
                foreach ($request->button_caption as $key => $button_caption) {
                    if ($button_caption !== null) {
                        $buttons = new MicrositeLinkButton;
                        $buttons->title = $button_caption;
                        $buttons->url = $request->button_link[$key];
                        $buttons->microsite_links_id = $data->id;
                        $buttons->save();
                    }
                }
            }

            if ($request->popunder_link_new) {
                foreach ($request->popunder_link_new as $key => $popunder_link) {
                    $pop_under = new MicrositePopUnder;
                    $pop_under->url = $popunder_link;
                    $pop_under->microsite_link_id = $data->id;
                    $pop_under->percentage = $request->percentage_new[$key];
                    $pop_under->precentage = $request->percentage_new[$key];
                    $pop_under->save();
                }
            }

            $filename = '';
            $filename2 = '';

            if ($request->file('images')) {
                if ($data->images) {
                    // $oldImagePath = str_replace('storage', 'public', $data->images);
                    Storage::delete($data->images);
                }
                $filename = $request->file('images')->store('public/microsite');
                $a = $request->file('images')->hashName();
                // $filename2 = $request->file('images')->store('storage/microsite');
            } else {
                $url = $data->images;
                $urlParts = explode('/', $url);
                $baseUrl = implode('/', array_slice($urlParts, 0, 5));
                if ($url === $baseUrl) {
                    $a = null;
                } else {
                    $a = basename($url);
                }
            }

            if ($filename) {
                $data->update([
                    'short_url' => $request->result_link,
                    'title' => $request->microsite_name,
                    'description' => $request->description,
                    'images' => $a,
                ]);
            } else {
                $data->update([
                    'short_url' => $request->result_link,
                    'title' => $request->microsite_name,
                    'description' => $request->description,
                    'images' => $a,
                ]);
            }

            foreach ($button as $index => $item) {
                $item->update([
                    'title' => $request->input('button_title.'.$index),
                    'url' => $request->input('button_url.'.$index),
                ]);
            }

            foreach ($popunder as $index => $item) {
                $item->url = $request->input('popunder_link.'.$index);
                $item->percentage = $request->input('percentage.'.$index);
                $item->precentage = $request->input('percentage.'.$index);
                $item->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Microsite berhasil diupdate.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat pembaruan.'.$e->getMessage()]);
        }
    }

    public function deleteButton($id)
    {
        try {
            $button = MicrositeLinkButton::findOrFail($id);
            $this->authorizeMicrosite($button->micrositeLink);
            $button->delete();

            return response()->json(['id' => $id, 'status' => '200'], 200);
        } catch (\Exception $e) {
            Log::error('Error Deleting Data:', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to delete data'], 500);
        }
    }

    public function deletePopunderId($id)
    {
        try {
            $popunder = MicrositePopUnder::findOrFail($id);
            $this->authorizeMicrosite($popunder->micrositeLink);
            $popunder->delete();

            return response()->json(['id' => $id, 'status' => '200'], 200);
        } catch (\Exception $e) {
            Log::error('Error Deleting Data:', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to delete data'], 500);
        }
    }

    public function deleteProfilePhoto($id)
    {
        try {
            $micrositeLink = MicrositeLink::findOrFail($id);
            $this->authorizeMicrosite($micrositeLink);
            $imagePath = $micrositeLink->images;
            if ($imagePath) {
                Storage::delete($imagePath);

                $micrositeLink->images = null;
                $micrositeLink->save();

                return response()->json(['success' => 'Gambar berhasil dihapus']);
            } else {
                return response()->json(['error' => 'Gambar tidak ditemukan'], 404);
            }

            return response()->json(['success' => 'Foto profil berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus foto profil'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $shorted = MicrositeLink::findOrFail($request->dataID);
            $this->authorizeMicrosite($shorted);
            $labelLinkIds = MicrositeLinkButton::where('microsite_links_id', $request->dataID)->pluck('id')->toArray();
            $shorted->delete();
            MicrositeLinkButton::whereIn('id', $labelLinkIds)->delete();
            MicrositePopUnder::where('microsite_link_id', $request->dataID)->delete();

            return response()->json(['message' => 'Data has been deleted'], 200);
        } catch (\Exception $e) {
            Log::error('Error Deleting Data:', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to delete data'], 500);
        }
    }

    private function authorizeMicrosite(MicrositeLink $microsite): void
    {
        abort_if($microsite->users_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);
    }
}

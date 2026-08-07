<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\DomainDecentralize;
use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Import Storage facade
use Illuminate\Support\Facades\Storage; // Pastikan menggunakan Facade Str
use Illuminate\Support\Str;

class BulkShortLinkController extends Controller
{
    public function list(Request $request)
    {
        $datasQuery = ShortLink::query()
            ->with(['user:id,name,user_label,role', 'campaign:id,name'])
            ->withSum('traffics as visitor', 'visitor_day')
            ->where('type_short_links', 'bulk');

        if (! Auth::user()->isAdmin()) {
            $datasQuery->where('user_id', Auth::id());
        }

        if (Auth::user()->team) {
            $tags = [Auth::user()->team->team_label, Auth::user()->user_label];
        } else {
            $tags = [Auth::user()->user_label];
        }
        $datas = $datasQuery->orderBy('created_at', 'desc')->get();
        $datas->each(fn ($data) => $data->visitor = (int) ($data->visitor ?? 0));
        $campaigns = Campaign::query()
            ->when(! Auth::user()->isAdmin(), fn ($query) => $query->where('user_id', Auth::id()))
            ->get();
        $domains = DomainDecentralize::orderBy('domain_url')->get();

        // 4. Hapus 'search' dari compact()
        return view('admin.bulkshortlink.index', compact('domains', 'tags', 'datas', 'campaigns')); // ->with(['search' => $search]) dihapus
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'original_urls' => 'required|string',
        ]);

        // 2. Memecah string menjadi array URL
        $urls = array_filter(explode("\n", $request->input('original_urls')), 'trim');

        $createdLinks = 0;
        $userId = auth()->id(); // Asumsi pengguna sudah login

        // *** 1. INISIALISASI VARIABEL HANYA SEKALI DI LUAR LOOP ***
        $linkStrings = [];
        // Variabel $newShortLinks dihapus karena tidak digunakan untuk alert

        $domain = DomainDecentralize::where('status', 'enable')->inRandomOrder()->first();
        if (! $domain) {
            return redirect()->back()->with('error', 'Tidak ada domain aktif untuk membuat short link.');
        }

        // 3. Iterasi dan Simpan Setiap URL
        foreach ($urls as $originalUrl) {
            $originalUrl = trim($originalUrl);

            // Pengecekan dasar apakah ini terlihat seperti URL valid
            if (! filter_var($originalUrl, FILTER_VALIDATE_URL)) {
                continue;
            }

            // 4. Membuat short_url unik
            $shortUrlCode = $this->generateUniqueShortUrl();

            // 5. Simpan ke Database
            $shortLink = ShortLink::create([
                'campaign_name' => 'Bulk Link - '.now()->format('YmdHis'),
                'description' => 'Dibuat secara bulk',
                'short_url' => $shortUrlCode,
                'original_url' => $originalUrl,
                'user_id' => $userId,
                'meta' => 'meta',
                'type_short_links' => 'bulk',
            ]);

            // *** 2. DEFINISIKAN $fullShortUrl SEBELUM DIGUNAKAN ***
            $fullShortUrl = ''.$domain->domain_url.'/'.$shortLink->short_url.'';

            // Tambahkan string untuk alert ke array
            $linkStrings[] = 'Short Link: '.$fullShortUrl.' -> Original: '.$originalUrl;

            $createdLinks++;
        }

        // 6. Redirect dengan membawa pesan sukses (untuk alert)
        if ($createdLinks > 0) {
            // Gabungkan semua string link dengan newline untuk alert
            $alertMessage = "{$createdLinks} short link berhasil dibuat secara bulk!\n\n".implode("\n", $linkStrings);

            return redirect()->back()
                ->with('success_alert_message', $alertMessage);
        }

        return redirect()->back()->with('error', 'Tidak ada URL valid yang berhasil dibuat.');
    }

    /**
     * Membuat kode short_url yang unik.
     *
     * * @return string
     */
    protected function generateUniqueShortUrl()
    {
        $length = 6; // Panjang kode yang diinginkan
        do {
            $code = Str::random($length);
        } while (ShortLink::where('short_url', $code)->exists()); // Pastikan belum ada di database

        return $code;
    }
}

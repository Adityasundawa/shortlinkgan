<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DomainDecentralize;
use App\Models\MicrositeLink;
use App\Models\MicrositeLinkTrafficts;
use App\Models\ShortLink;
use App\Models\ShortLinkTraffict;
use Carbon\Carbon;
use Illuminate\Http\Request; // Import Carbon untuk bekerja dengan tanggal
use Illuminate\Support\Facades\Schema;

class TraffictController extends Controller
{
    public function newTraffict(Request $request)
    {
        $validatedData = $request->validate([
            'url_code' => 'required|string',
            'domain' => 'required|string', // <-- Perubahan di sini
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'device_type' => 'required|string|max:255',
            'fingerprint_id' => 'required|string|max:255',
            'utm_campaign' => 'nullable|string|max:255',
            'utm_medium' => 'nullable|string|max:255',
            'utm_source' => 'nullable|string|max:255',
            'utm_content' => 'nullable|string|max:255',
            'utm_term' => 'nullable|string|max:255',
        ]);

        // Langkah 2: Cari ShortLink dan Domain berdasarkan input.
        $shortLink = ShortLink::where('short_url', $validatedData['url_code'])->first();
        $domainDecentralize = DomainDecentralize::where('domain_url', $validatedData['domain'])->first();

        // Langkah 3: Handle jika salah satu atau keduanya tidak ditemukan.
        if (! $shortLink) {
            return response()->json(['message' => 'The provided URL code was not found.'], 404);
        }
        if (! $domainDecentralize) {
            return response()->json(['message' => 'The provided domain is not registered.'], 422);
        }

        // Langkah 4: Tentukan status unique visitor.
        $today = Carbon::today()->toDateString();

        $hasFingerprintColumn = Schema::hasColumn('short_link_trafficts', 'fingerprint_id');
        $isExistingVisitorToday = $hasFingerprintColumn
            ? ShortLinkTraffict::where('fingerprint_id', $validatedData['fingerprint_id'])
                ->where('short_links_id', $shortLink->id)
                ->where('date', $today)
                ->exists()
            : false;

        $uniqueStatus = $isExistingVisitorToday ? 'no' : 'yes';

        // Langkah 5: Gabungkan semua data untuk disimpan.
        $dataToCreate = array_merge($validatedData, [
            'short_links_id' => $shortLink->id,
            'domain_decentralizes_id' => $domainDecentralize->id, // <-- Gunakan id dari domain
            'unique_visitor_day' => $uniqueStatus,
            'visitor_day' => 1,
            'date' => $today,
        ]);

        // Hapus data request yang tidak ada di tabel ShortLinkTraffic.
        unset($dataToCreate['url_code'], $dataToCreate['domain']);
        if (! $hasFingerprintColumn) {
            unset($dataToCreate['fingerprint_id']);
        }
        $this->removeMissingUtmColumns('short_link_trafficts', $dataToCreate);

        // Langkah 6: Buat record baru.
        $traffic = ShortLinkTraffict::create($dataToCreate);

        return response()->json($traffic, 201);

    }

    public function traffictmicrosite(Request $request)
    {
        $validatedData = $request->validate([
            'url_code' => 'required|string',
            'domain' => 'required|string', // <-- Perubahan di sini
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'device_type' => 'required|string|max:255',
            'fingerprint_id' => 'required|string|max:255',
            'utm_campaign' => 'nullable|string|max:255',
            'utm_medium' => 'nullable|string|max:255',
            'utm_source' => 'nullable|string|max:255',
            'utm_content' => 'nullable|string|max:255',
            'utm_term' => 'nullable|string|max:255',
        ]);

        // Langkah 2: Cari ShortLink dan Domain berdasarkan input.
        $shortLink = MicrositeLink::where('short_url', $validatedData['url_code'])->first();
        $domainDecentralize = DomainDecentralize::where('domain_url', $validatedData['domain'])->first();

        // Langkah 3: Handle jika salah satu atau keduanya tidak ditemukan.
        if (! $shortLink) {
            return response()->json(['message' => 'The provided URL code was not found.'], 404);
        }
        if (! $domainDecentralize) {
            return response()->json(['message' => 'The provided domain is not registered.'], 422);
        }

        // Langkah 4: Tentukan status unique visitor.
        $today = Carbon::today()->toDateString();

        $hasFingerprintColumn = Schema::hasColumn('microsite_link_trafficts', 'fingerprint_id');
        $isExistingVisitorToday = $hasFingerprintColumn
            ? MicrositeLinkTrafficts::where('fingerprint_id', $validatedData['fingerprint_id'])
                ->where('microsite_links_id', $shortLink->id)
                ->where('date', $today)
                ->exists()
            : false;

        $uniqueStatus = $isExistingVisitorToday ? 'no' : 'yes';

        // Langkah 5: Gabungkan semua data untuk disimpan.
        $dataToCreate = array_merge($validatedData, [
            'microsite_links_id' => $shortLink->id,
            'domain_decentralizes_id' => $domainDecentralize->id, // <-- Gunakan id dari domain
            'unique_visitor_day' => $uniqueStatus,
            'visitor_day' => 1,
            'date' => $today,
        ]);

        // Hapus data request yang tidak ada di tabel ShortLinkTraffic.
        unset($dataToCreate['url_code'], $dataToCreate['domain']);
        if (! $hasFingerprintColumn) {
            unset($dataToCreate['fingerprint_id']);
        }
        $this->removeMissingUtmColumns('microsite_link_trafficts', $dataToCreate);

        // Langkah 6: Buat record baru.
        $traffic = MicrositeLinkTrafficts::create($dataToCreate);

        return response()->json($traffic, 201);

    }

    private function removeMissingUtmColumns(string $table, array &$data): void
    {
        foreach (['utm_campaign', 'utm_medium', 'utm_source', 'utm_content', 'utm_term'] as $column) {
            if (! Schema::hasColumn($table, $column)) {
                unset($data[$column]);
            }
        }
    }
}

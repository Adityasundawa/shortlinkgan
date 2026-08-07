<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\LabelShortlink;
use App\Models\ShortLink;
use App\Models\ShortLinkTraffict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShortedLinkController extends Controller
{
    public function shorted()
    {
        $data = ShortLink::with('labelShortlinks')
            ->when(! Auth::user()->isAdmin(), fn ($query) => $query->where('user_id', Auth::id()))
            ->get();
        $a = 0;
        foreach ($data as $item) {
            $labellink = LabelShortlink::where('short_links_id', $item->id)->get();
            foreach ($labellink as $key) {
                $a = Label::where('id', $key->labels_id)->get();
            }
        }

        return view('admin.shortedlink.index', compact('data', 'a'));
    }

    public function shortedstatistic($id)
    {
        $a = ShortLink::findOrFail($id);
        abort_if($a->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);
        $c = ShortLinkTraffict::where('short_links_id', $id)->get();
        $get = ShortLinkTraffict::where('short_links_id', $id)->get();

        $count = $c->sum('unique_visitor_day');
        $count2 = $c->sum('visitor_day');

        $all = $count + $count2;

        return view('admin.shortedlink.statistic', compact('a', 'all', 'count', 'get'));
    }

    public function destroy(Request $request)
    {
        try {
            $shorted = ShortLink::findOrFail($request->dataID);
            abort_if($shorted->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);
            $labelLinkIds = LabelShortlink::where('short_links_id', $request->dataID)->pluck('id')->toArray();
            $shorted->delete();
            LabelShortlink::whereIn('id', $labelLinkIds)->delete();

            return response()->json(['message' => 'Data has been deleted'], 200);
        } catch (\Exception $e) {
            Log::error('Error Deleting Data:', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to delete data'], 500);
        }
    }

    public function getDataById($id)
    {
        try {
            $data = ShortLink::with('popUnders')->findOrFail($id);
            abort_if($data->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);

            $labels = [];

            $labellinks = LabelShortlink::where('short_links_id', $data->id)->get();

            foreach ($labellinks as $labellink) {
                $label = Label::find($labellink->labels_id);

                $labels[] = $label;
            }

            // Menambahkan array label ke data
            $data->labels = $labels;
            $data->redirects = $data->popUnders;
            $data->is_multi_redirect = $data->is_popunder === 'yes'
                || $data->original_url === 'multi-redirect-link'
                || $data->popUnders->isNotEmpty();

            Log::info('Data berhasil diambil:', ['data' => $data]);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error:', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to fetch data.'], 500);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'campaign_name' => 'required|string',
                'description' => 'required|string',
                'tags' => 'required|string',
                'original_url' => 'required|string',
            ]);

            $data = ShortLink::findOrFail($id);
            abort_if($data->user_id !== Auth::id() && ! Auth::user()->isAdmin(), 403);
            $tags = $request->input('tags');
            $tagsArray = explode(',', $tags);

            if (! empty($tagsArray)) {
                // Mendapatkan semua tag yang terkait dengan ShortLink saat ini
                $currentTags = LabelShortlink::where('short_links_id', $id)->get();

                foreach ($currentTags as $currentTag) {
                    $tagExists = in_array($currentTag->label->label_name, $tagsArray);

                    // Jika tag saat ini tidak ada dalam array tags yang baru, hapus hubungannya
                    if (! $tagExists) {
                        $currentTag->delete();
                    }
                }

                foreach ($tagsArray as $tag) {
                    $existingTag = Label::where('label_name', trim($tag))->first();

                    if (! $existingTag) {
                        $newTag = Label::create([
                            'label_name' => trim($tag),
                        ]);

                        LabelShortlink::create([
                            'labels_id' => $newTag->id,
                            'users_id' => $data->user_id,
                            'short_links_id' => $id,
                        ]);
                    } else {
                        $existingRelation = LabelShortlink::where([
                            'labels_id' => $existingTag->id,
                            'users_id' => $data->user_id,
                            'short_links_id' => $id,
                        ])->first();

                        if (! $existingRelation) {
                            LabelShortlink::create([
                                'labels_id' => $existingTag->id,
                                'users_id' => $data->user_id,
                                'short_links_id' => $id,
                            ]);
                        }
                    }
                }
            }

            $cek = ShortLink::where('campaign_name', $request->campaign_name)
                ->where('id', '!=', $id)
                ->first();

            if ($cek) {
                return response()->json(['status' => 'error', 'message' => 'Nama sudah digunakan.'], 200);
            }

            // Jika tidak ada, lakukan pembaruan seperti biasa
            $data->update([
                'campaign_name' => $request->campaign_name,
                'description' => $request->description,
                'original_url' => $request->original_url,
                'short_url' => $data->short_url,
                'user_id' => $data->user_id,
            ]);

            Log::info('Data berhasil update:', ['data' => $data]);

            return response()->json(['status' => 'success', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat pembaruan.'], 200);
        }
    }
}

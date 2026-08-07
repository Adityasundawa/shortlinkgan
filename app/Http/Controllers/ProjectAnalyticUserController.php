<?php

namespace App\Http\Controllers;

use App\Models\DomainDecentralize;
use App\Models\Label;
use App\Models\LabelShortlink;
use App\Models\ShortLink;
use App\Models\ShortLinkTraffict;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectAnalyticUserController extends Controller
{
    public function list(Request $request)
    {
        $search = $request->input('search');

        $datasQuery = ShortLink::query()
            ->with('user:id,name,user_label,role')
            ->withSum('traffics as visitor', 'visitor_day')
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('campaign_name', 'like', "%$search%")
                    ->orWhere('original_url', 'like', "%$search%")
                    ->orWhere('short_url', 'like', "%$search%")
                    ->orWhereHas('user', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%$search%")
                            ->orWhere('user_label', 'like', "%$search%");
                    });
            });

        if (Auth::user()->team) {
            $tags = [Auth::user()->team->team_label, Auth::user()->user_label];
        } else {
            $tags = [Auth::user()->user_label];
        }

        $datas = $datasQuery->orderBy('created_at', 'desc')->paginate(25);
        $datas->through(fn ($data) => tap($data, fn ($data) => $data->visitor = (int) ($data->visitor ?? 0)));

        $domains = DomainDecentralize::orderBy('domain_url')->get();

        return view('admin.analytic_user.list', compact('domains', 'tags', 'datas'))->with(['search' => $search]);
    }

    public function update(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'data_id' => 'required|integer',
            'name' => 'required',
            'tags' => 'required',
            'url' => 'required',
            'link_code' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 500);
        }

        // cari dupicate code link
        $duplicate_code_link = ShortLink::where('id', '!=', $request->data_id)
            ->where('short_url', $request->link_code);
        if ($duplicate_code_link->count() >= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode Link sudah terpakai. Gunakan kode lain',
            ], 500);
        }

        $shortLink = ShortLink::where('id', $request->data_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $shortLink->update([
            'campaign_name' => $request->name,
            'description' => $request->description,
            'original_url' => $request->url,
            'short_url' => $request->link_code,
            'utm_campaign' => $request->utm_campaign,
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_content' => $request->utm_content,
            'utm_term' => $request->utm_term,
        ]);

        LabelShortlink::where('short_links_id', $request->data_id)->delete();
        $tags = explode(',', $request->tags);
        foreach ($tags as $label) {
            $labelModel = Label::firstOrCreate(['label_name' => trim($label)]);
            LabelShortlink::create([
                'users_id' => Auth::user()->id,
                'short_links_id' => $request->data_id,
                'labels_id' => $labelModel->id,
            ]);
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
        $shortLink = ShortLink::where('id', $request->dataID)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $shortLink->delete();
        LabelShortlink::where('short_links_id', $request->dataID)->delete();

        return response()->json(['status' => 'success', 'message' => 'data has been deleted'], 200);
    }

    public function nalytic(Request $request, $project_id)
    {
        $detail = ShortLink::where('id', $project_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $domains = DomainDecentralize::where('status', 'enable')->orderBy('domain_url')->get();
        $linktraffic = [];
        foreach ($domains as $domain) {
            // Ambil data lalu lintas untuk domain tertentu
            $traffic = ShortLinkTraffict::where('short_links_id', $detail->id)
                ->where('domain_decentralizes_id', $domain->id)
                ->get();

            // Iterasi setiap data lalu lintas untuk domain ini
            foreach ($traffic as $data) {
                $linktraffic[] = [
                    'date' => $data->date,
                    'domain' => $domain->domain_url,
                    'visitors' => $data->visitor_day,
                ];
            }
        }

        return view('admin.analytic_user.analytic')->with([
            'detail' => $detail,
            'domains' => $domains,
            'linktraffic' => $linktraffic,
        ]);
    }

    public function analyticJSON(Request $request, $project_id)
    {
        try {
            $dateStart = Carbon::parse($request->start)->format('Y-m-d');
            $dateEnd = Carbon::parse($request->end)->format('Y-m-d');

            $dates = [
                'raw' => [],
                'carbon' => [],
            ];

            $dataJSON = [
                'all' => [],
            ];

            $shortLink = ShortLink::where('id', $project_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $dailyVisitors = ShortLinkTraffict::query()
                ->selectRaw('DATE(date) as traffic_date, SUM(visitor_day) as total_visitors')
                ->where('short_links_id', $shortLink->id)
                ->whereBetween('date', [$dateStart, $dateEnd])
                ->groupBy('traffic_date')
                ->orderBy('traffic_date', 'asc')
                ->get();

            foreach ($dailyVisitors as $visitor) {
                $dates['raw'][] = $visitor->traffic_date;
                $dates['carbon'][] = Carbon::parse($visitor->traffic_date)->format('d M');
                $dataJSON['all'][] = (int) $visitor->total_visitors;
            }

            $domains = DomainDecentralize::where('status', 'enable')
                ->orderBy('domain_url', 'asc')
                ->get(['id', 'domain_url']);

            $trafficPerDomain = ShortLinkTraffict::query()
                ->select('domain_decentralizes_id', DB::raw('SUM(visitor_day) as total_visitors'))
                ->where('short_links_id', $shortLink->id)
                ->whereBetween('date', [$dateStart, $dateEnd])
                ->groupBy('domain_decentralizes_id')
                ->pluck('total_visitors', 'domain_decentralizes_id');

            foreach ($domains as $domain) {
                $host = parse_url($domain->domain_url, PHP_URL_HOST) ?: $domain->domain_url;
                $dataJSON[$host] = [(int) ($trafficPerDomain[$domain->id] ?? 0)];
            }

            return response()->json(['dates' => $dates, 'dataJSON' => $dataJSON]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

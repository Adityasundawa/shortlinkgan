<?php

namespace App\Http\Controllers;

use App\Helpers\UrlShort;
use App\Helpers\ResponseFormatter;
use App\Models\DomainDecentralize;
use App\Models\Label;
use App\Models\LabelShortlink;
use App\Models\ShortLink;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function dashboard() {
        $data['labels'] =  Team::find(Auth::user()->id_team);
        $data['domains'] = DomainDecentralize::get();
        return view('admin.index', $data);
    }
}

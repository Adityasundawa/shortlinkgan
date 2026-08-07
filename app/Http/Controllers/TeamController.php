<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function list(){
        return view('admin.team_management.list');
    }
}

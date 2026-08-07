<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamManagementController extends Controller
{
    public function create()
    {
        $teams = Team::all();

        return view('admin.create_new_team.createteam');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'teamname' => 'required',
            'teamlabel' => 'required',
        ]);

        Team::create([
            'team_name' => $request['teamname'],
            'team_slug' => Str::slug($request['teamname']),
            'team_label' => $request['teamlabel'],
        ]);
        $teams = Team::all();

        // Alert::success('Berhasil', 'Berhasil menambahkan data');
        // return view('admin.list_team.listteam', compact('teams'));
        return redirect()->route('admin.list_team');
    }

    public function list()
    {

        $teams = Team::all();

        return view('admin.list_team.listteam', compact('teams'));
    }

    public function edit(int $id)
    {
        $team = Team::findOrFail($id);

        return view('admin.edit_team.editteam', compact('team'));
    }

    public function update(int $id, Request $request)
    {
        $team = Team::findOrFail($id);
        $team->update([
            'team_name' => $request['teamname'],
            'team_slug' => $request['teamslug'],
            'team_label' => $request['teamlabel'],
        ]);

        return redirect()->route('admin.list_team');
    }

    public function delete($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return redirect()->route('admin.list_team')->with('success', 'Team berhasil dihapus.');
    }
}

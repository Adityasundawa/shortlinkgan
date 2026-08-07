<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function create()
    {

        $teamss = Team::all();
        $users = User::all();
        $roless = User::select('role')->distinct()->get();

        return view('admin.create_new_user.create', compact('teamss', 'users', 'roless'));
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'team' => 'required',
            'name' => 'required',
            'username' => 'required',
            'userlabel' => 'required|max:5',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        $request->validate(User::$rules);

        User::create([
            'id_team' => $request['team'],
            'name' => $request['name'],
            'username' => $request['username'],
            'user_label' => $request['userlabel'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role' => $request['role'],
        ]);
        $users = User::all();

        // Alert::success('Berhasil', 'Berhasil menambahkan data');
        // return view('admin.list_user.listuser', compact('users'));
        return redirect()->route('admin.list_user');
    }

    public function checkDuplicate(Request $request)
    {
        $fieldName = key($request->all());
        $value = $request->input($fieldName);

        if ($fieldName === 'email' && User::where('email', $value)->exists()) {
            return response()->json(['message' => 'Email sudah ada, silakan pilih email lain.'], 422);
        }

        if ($fieldName === 'username' && User::where('username', $value)->exists()) {
            return response()->json(['message' => 'Username sudah ada, silakan pilih username lain.'], 422);
        }

        return response()->json(['message' => ''], 200);
    }

    public function list()
    {

        $users = User::all();

        return view('admin.list_user.listuser', compact('users'));
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        $roless = User::select('role')->distinct()->get();
        $teamss = Team::all();

        return view('admin.edit_user.edit', compact('user', 'teamss', 'roless'));
    }

    public function update(int $id, Request $request)
    {
        $user = User::findOrFail($id);
        $updateData = [
            'id_team' => $request['team'],
            'name' => $request['name'],
            'username' => $request['username'],
            'user_label' => $request['userlabel'],
            'email' => $request['email'],
            'role' => $request['role'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.list_user');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.list_user')->with('success', 'User berhasil dihapus.');
    }
}

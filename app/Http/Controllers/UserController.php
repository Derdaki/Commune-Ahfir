<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::orderBy('name')->get()]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', Rule::in(['admin', 'employee', 'citizen'])]]);
        abort_if($user->is($request->user()) && $data['role'] !== 'admin', 422, __('app.flash.self_role'));
        $user->update($data);

        return back()->with('success', __('app.flash.role_updated'));
    }
}

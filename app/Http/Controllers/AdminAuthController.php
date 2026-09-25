<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminAuthController extends Controller
{
    public function form() { return view('admin.login'); }
    public function login(Request $request)
    {
        $data = $request->validate(['email'=>['required','email'],'password'=>['required','string']]);
        $user = User::where('email', $data['email'])->first();
        if (! $user && $data['email'] === env('ADMIN_EMAIL') && env('ADMIN_PASSWORD_HASH') && Hash::check($data['password'], env('ADMIN_PASSWORD_HASH'))) {
            $user = User::create(['name' => 'Administrator', 'email' => $data['email'], 'password' => $data['password'], 'role' => 'admin']);
        }
        if (! $user || ! Hash::check($data['password'], $user->password)) return back()->withErrors(['email'=>'The admin credentials are incorrect.'])->onlyInput('email');
        $request->session()->regenerate(); $request->session()->put(['skinoveda_admin_id' => $user->id, 'skinoveda_admin_role' => $user->role]);
        return redirect()->route('admin.dashboard');
    }
    public function logout(Request $request) { $request->session()->forget(['skinoveda_admin_id', 'skinoveda_admin_role']); $request->session()->regenerateToken(); return redirect()->route('admin.login'); }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password lama salah.',
        ]);

        $request->user()->update([
            'password' => $request->password,
            'must_change_password' => false,
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}

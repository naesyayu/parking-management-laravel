<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChangePasswordController extends Controller
{
    /**
     * Show change password form
     */
    public function show()
    {
        return view('pages.change-password'); // sebelumnya 'user.password'
    }

    /**
     * Update password
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!\Illuminate\Support\Facades\Hash::check($value, auth()->user()->password)) {
                        $fail('Password lama tidak sesuai.');
                    }
                },
            ],
            'new_password' => ['required', 'confirmed'], // hapus min & regex
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
        ]);

        $user = auth()->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $user->save();

        return redirect()->route('dashboard.index')->with('success', 'Password berhasil diubah.');
    }
}

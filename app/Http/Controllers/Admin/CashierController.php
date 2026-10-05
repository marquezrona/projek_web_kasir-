<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CashierController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('createCashier', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $cashier = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'kasir',
        ]);
        $cashier->forceFill(['email_verified_at' => now()])->save();

        return to_route('admin.kasir')->with('cashierCreated', 'Akun kasir berhasil ditambahkan.');
    }
}

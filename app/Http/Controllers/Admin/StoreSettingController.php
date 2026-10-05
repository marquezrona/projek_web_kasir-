<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class StoreSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.pengaturan', [
            'storeSettings' => StoreSetting::current(),
        ]);
    }

    public function updateStore(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('storeSettings', [
            'store_name' => ['required', 'string', 'max:150'],
            'store_address' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $settings = StoreSetting::current();
        $previousLogo = $settings->logo_path;

        if ($request->hasFile('logo')) {
            $newLogo = $request->file('logo')->store('store-logos', 'public');

            if (! $newLogo) {
                throw new RuntimeException('Logo toko gagal disimpan.');
            }

            $settings->logo_path = $newLogo;
        }

        $settings->store_name = $data['store_name'];
        $settings->store_address = $data['store_address'];
        $settings->save();

        if ($previousLogo && $previousLogo !== $settings->logo_path) {
            Storage::disk('public')->delete($previousLogo);
        }

        return to_route('admin.pengaturan')
            ->with('storeSettingsStatus', 'Informasi toko berhasil diperbarui.');
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();
        $emailChanged = strtolower($request->input('email', '')) !== $user->email;
        $passwordChanged = filled($request->input('password'));

        $data = $request->validateWithBag('accountSettings', [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password' => [
                Rule::requiredIf($emailChanged || $passwordChanged),
                'current_password',
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->email = $data['email'];

        if ($passwordChanged) {
            $user->password = $data['password'];
        }

        $user->save();

        return to_route('admin.pengaturan')
            ->with('accountSettingsStatus', 'Pengaturan akun Admin berhasil diperbarui.');
    }
}

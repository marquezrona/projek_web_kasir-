@extends('layouts.admin')

@section('title', 'Pengaturan Toko')

@push('styles')
    <style>
        .password-field { position: relative; }
        .password-field .form-control { padding-right: 46px; }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            padding: 4px;
            border: 0;
            transform: translateY(-50%);
            color: #68727a;
            background: transparent;
            cursor: pointer;
        }
        .password-toggle:hover, .password-toggle:focus-visible { color: #8d2a22; }
        .password-toggle:focus-visible { outline: 2px solid #b94245; outline-offset: 2px; }
    </style>
@endpush

@section('content')
    <header class="page-heading">
        <h1>Pengaturan</h1>
        <p>Atur identitas toko dan kredensial akun Admin.</p>
    </header>

    @if(session('storeSettingsStatus'))
        <div class="alert alert-success" role="status">{{ session('storeSettingsStatus') }}</div>
    @endif

    <section class="admin-card mb-4">
        <div class="admin-card-header">
            <div>
                <h2>Informasi Toko</h2>
                <span class="text-muted small">Nama, alamat, dan logo yang tampil di aplikasi.</span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pengaturan.toko') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="admin-card-body">
                @if($errors->storeSettings->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->storeSettings->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label" for="store_name">Nama Toko</label>
                    <input
                        class="form-control"
                        id="store_name"
                        name="store_name"
                        type="text"
                        value="{{ old('store_name', $storeSettings->store_name) }}"
                        maxlength="150"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="store_address">Alamat Toko</label>
                    <input
                        class="form-control"
                        id="store_address"
                        name="store_address"
                        type="text"
                        value="{{ old('store_address', $storeSettings->store_address) }}"
                        maxlength="255"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label d-block" for="logo">Logo Toko</label>
                    <img
                        class="mb-3 rounded-circle border bg-white"
                        src="{{ $storeSettings->logo_url }}"
                        alt="Logo {{ $storeSettings->store_name }}"
                        width="112"
                        height="112"
                        style="object-fit: cover"
                    >
                    <input class="form-control" id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">Format JPG, PNG, atau WebP. Ukuran maksimal 2 MB.</div>
                </div>

                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-floppy me-1" aria-hidden="true"></i>Simpan Informasi Toko
                </button>
            </div>
        </form>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Akun Admin</h2>
                <span class="text-muted small">Email digunakan untuk login. Kata sandi baru bersifat opsional.</span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pengaturan.akun') }}">
            @csrf
            @method('PUT')
            <div class="admin-card-body">
                @if(session('accountSettingsStatus'))
                    <div class="alert alert-success" role="status">{{ session('accountSettingsStatus') }}</div>
                @endif

                @if($errors->accountSettings->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->accountSettings->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label" for="email">Email Login Admin</label>
                    <input
                        class="form-control"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        maxlength="255"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="current_password">Kata Sandi Saat Ini</label>
                    <div class="password-field">
                        <input
                            class="form-control"
                            id="current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                        >
                        <button class="password-toggle" type="button" data-password-target="current_password" data-password-label="kata sandi saat ini" aria-label="Tampilkan kata sandi saat ini" aria-pressed="false">
                            <i class="bi bi-eye-slash" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="form-text">Wajib diisi jika email atau kata sandi diubah.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="password">Kata Sandi Baru</label>
                        <div class="password-field">
                            <input
                                class="form-control"
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                            >
                            <button class="password-toggle" type="button" data-password-target="password" data-password-label="kata sandi baru" aria-label="Tampilkan kata sandi baru" aria-pressed="false">
                                <i class="bi bi-eye-slash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <div class="password-field">
                            <input
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                            >
                            <button class="password-toggle" type="button" data-password-target="password_confirmation" data-password-label="konfirmasi kata sandi baru" aria-label="Tampilkan konfirmasi kata sandi" aria-pressed="false">
                                <i class="bi bi-eye-slash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-shield-lock me-1" aria-hidden="true"></i>Simpan Akun Admin
                </button>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.password-toggle').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const password = document.getElementById(toggle.dataset.passwordTarget);
                const visible = password.type === 'text';

                password.type = visible ? 'password' : 'text';
                toggle.setAttribute('aria-pressed', String(!visible));
                toggle.setAttribute('aria-label', `${visible ? 'Tampilkan' : 'Sembunyikan'} ${toggle.dataset.passwordLabel}`);
                toggle.querySelector('i').className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        });
    </script>
@endpush

@extends('layouts.admin')

@section('title', 'Kelola Kasir')

@push('styles')
    <style>
        .cashier-password-field { position: relative; }
        .cashier-password-field .form-control { padding-right: 46px; }
        .cashier-password-toggle {
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
        .cashier-password-toggle:hover, .cashier-password-toggle:focus-visible { color: #07536a; }
        .cashier-password-toggle:focus-visible { outline: 2px solid #07536a; outline-offset: 2px; }
    </style>
@endpush

@section('content')
    <header class="page-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1>Kelola Kasir</h1>
                <p>Daftar akun yang dapat mengakses sistem kasir</p>
            </div>
            <button
                class="btn btn-primary"
                id="cashierFormToggle"
                type="button"
                aria-expanded="{{ $errors->createCashier->any() ? 'true' : 'false' }}"
                aria-controls="tambah-akun-kasir"
            >
                <i class="bi bi-person-plus me-1" aria-hidden="true"></i>Tambah Akun Kasir
            </button>
        </div>
    </header>

    @if(session('cashierCreated'))
        <div class="alert alert-success" role="status">{{ session('cashierCreated') }}</div>
    @endif

    <section
        class="admin-card mb-4"
        id="tambah-akun-kasir"
        @if(!$errors->createCashier->any()) hidden @endif
    >
        <div class="admin-card-header">
            <div>
                <h2>Tambah Akun Kasir</h2>
                <span class="text-muted small">Akun baru akan memiliki akses sebagai kasir.</span>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.kasir.store') }}">
            @csrf
            <div class="admin-card-body">
                @if($errors->createCashier->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->createCashier->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="name">Nama Kasir</label>
                        <input
                            class="form-control"
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            maxlength="255"
                            autocomplete="name"
                            required
                        >
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="email">Email Login</label>
                        <input
                            class="form-control"
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            maxlength="255"
                            autocomplete="username"
                            required
                        >
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="password">Kata Sandi</label>
                        <div class="cashier-password-field">
                            <input
                                class="form-control"
                                id="password"
                                name="password"
                                type="password"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                            <button
                                class="cashier-password-toggle"
                                type="button"
                                data-password-target="password"
                                data-password-label="kata sandi"
                                aria-label="Tampilkan kata sandi"
                                aria-pressed="false"
                            >
                                <i class="bi bi-eye-slash" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <div class="cashier-password-field">
                            <input
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                            <button
                                class="cashier-password-toggle"
                                type="button"
                                data-password-target="password_confirmation"
                                data-password-label="konfirmasi kata sandi"
                                aria-label="Tampilkan konfirmasi kata sandi"
                                aria-pressed="false"
                            >
                                <i class="bi bi-eye-slash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-person-plus me-1" aria-hidden="true"></i>Simpan Akun Kasir
                </button>
            </div>
        </form>
    </section>

    <section class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <h2>Akun Pengguna</h2>
            <span class="text-secondary small">{{ $cashiers->count() }} akun</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cashiers as $cashier)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $cashier->name }}</td>
                            <td>{{ $cashier->email }}</td>
                            <td>
                                <span class="badge {{ $cashier->role === 'admin' ? 'text-bg-dark' : 'text-bg-success' }}">
                                    {{ ucfirst($cashier->role) }}
                                </span>
                            </td>
                            <td><span class="badge text-bg-light border text-secondary">Aktif</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-5 text-center text-secondary">Belum ada akun pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.cashier-password-toggle').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const password = document.getElementById(toggle.dataset.passwordTarget);
                const visible = password.type === 'text';

                password.type = visible ? 'password' : 'text';
                toggle.setAttribute('aria-pressed', String(!visible));
                toggle.setAttribute('aria-label', `${visible ? 'Tampilkan' : 'Sembunyikan'} ${toggle.dataset.passwordLabel}`);
                toggle.querySelector('i').className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        });

        const cashierFormToggle = document.querySelector('#cashierFormToggle');
        const cashierForm = document.querySelector('#tambah-akun-kasir');

        cashierFormToggle?.addEventListener('click', () => {
            const isExpanded = cashierFormToggle.getAttribute('aria-expanded') === 'true';
            cashierForm.hidden = isExpanded;
            cashierFormToggle.setAttribute('aria-expanded', String(!isExpanded));
            cashierFormToggle.innerHTML = isExpanded
                ? '<i class="bi bi-person-plus me-1" aria-hidden="true"></i>Tambah Akun Kasir'
                : '<i class="bi bi-x-lg me-1" aria-hidden="true"></i>Tutup Formulir';
        });
    </script>
@endpush

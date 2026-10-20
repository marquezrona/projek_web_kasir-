@extends('layouts.admin')

@section('title', 'Member NFC')

@section('content')
    <div class="page-heading">
        <h1>Member NFC</h1>
        <p>Daftarkan pelanggan sebagai member untuk mendapatkan kartu NFC dan mengisi saldo belanja.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <section class="admin-card h-100">
                <header class="admin-card-header">
                    <h2><i class="bi bi-person-plus me-2" aria-hidden="true"></i>Daftarkan Member dan Kartu</h2>
                </header>
                <div class="admin-card-body">
                    <form method="POST" action="{{ route('cashier.pelanggan.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="customerName">Nama pelanggan</label>
                            <input class="form-control" id="customerName" name="name" type="text" maxlength="255" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="customerPhone">Nomor telepon <span class="text-muted">(opsional)</span></label>
                            <input class="form-control" id="customerPhone" name="phone" type="tel" maxlength="30" value="{{ old('phone') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="customerCardUid">UID kartu NFC</label>
                            <input class="form-control" id="customerCardUid" name="card_uid" type="text" maxlength="100" value="{{ old('card_uid') }}" placeholder="Tempel kartu pada reader atau ketik UID" required>
                            <div class="form-text">Reader USB yang berfungsi sebagai keyboard dapat mengisi UID saat kolom ini aktif. Untuk simulasi, gunakan UID unik seperti DEMO-KARTU-001.</div>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-person-check me-1" aria-hidden="true"></i>Daftarkan sebagai Member</button>
                    </form>
                </div>
            </section>
        </div>

        <div class="col-xl-5">
            <section class="admin-card h-100">
                <header class="admin-card-header">
                    <h2><i class="bi bi-wallet2 me-2" aria-hidden="true"></i>Isi Saldo Kartu</h2>
                </header>
                <div class="admin-card-body">
                    @if ($customers->isEmpty())
                        <p class="mb-0">Daftarkan pelanggan beserta kartu NFC terlebih dahulu untuk mengisi saldo.</p>
                    @else
                        <form method="POST" action="{{ route('cashier.pelanggan.top-up', ['customer' => '__CUSTOMER__']) }}" id="customerTopUpForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="topUpCustomer">Member / kartu</label>
                                <select class="form-select" id="topUpCustomer" required>
                                    <option value="">Pilih pelanggan dan kartu</option>
                                    @foreach ($customers as $customer)
                                        @if ($customer->nfcCard)
                                            <option value="{{ $customer->id }}" data-balance="{{ $customer->nfcCard->balance }}" @selected(old('customer_id') == $customer->id)>
                                                {{ $customer->name }} — {{ $customer->nfcCard->uid }} (Saldo {{ 'Rp '.number_format((float) $customer->nfcCard->balance, 0, ',', '.') }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="topUpAmount">Nominal top-up (Rp)</label>
                                <input class="form-control" id="topUpAmount" name="amount" type="number" min="1" step="1" value="{{ old('amount', 50000) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="topUpPaymentMethod">Metode pembayaran</label>
                                <select class="form-select" id="topUpPaymentMethod" name="payment_method" required>
                                    <option value="tunai" @selected(old('payment_method', 'tunai') === 'tunai')>Tunai</option>
                                    <option value="qris" @selected(old('payment_method') === 'qris')>QRIS simulasi</option>
                                    <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer simulasi</option>
                                </select>
                            </div>
                            <div class="alert alert-warning small">Pembayaran QRIS dan transfer di halaman demo ini tidak diproses sungguhan.</div>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle me-1" aria-hidden="true"></i>Tambahkan Saldo</button>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <section class="admin-card">
        <header class="admin-card-header d-flex align-items-center justify-content-between gap-3">
            <div>
                <h2>Daftar Member dan Kartu</h2>
                <p class="mb-0 mt-1">Member terdaftar: {{ $customers->total() }}</p>
            </div>
            <span class="badge text-bg-warning">SALDO DEMO</span>
        </header>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Telepon</th>
                        <th>UID Kartu</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="fw-semibold">{{ $customer->name }} <span class="badge text-bg-success">Member</span></td>
                            <td>{{ $customer->phone ?: '—' }}</td>
                            <td><code>{{ $customer->nfcCard?->uid ?? 'Belum ada kartu' }}</code></td>
                            <td class="text-end fw-semibold">
                                {{ $customer->nfcCard ? 'Rp '.number_format((float) $customer->nfcCard->balance, 0, ',', '.') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Belum ada member. Daftarkan pelanggan melalui formulir di atas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($customers->hasPages())
            <div class="p-3">{{ $customers->links() }}</div>
        @endif
    </section>

    <script>
        const customerTopUpForm = document.querySelector('#customerTopUpForm');
        if (customerTopUpForm) {
            customerTopUpForm.addEventListener('submit', event => {
                const customerId = document.querySelector('#topUpCustomer').value;
                if (!customerId) {
                    event.preventDefault();
                    alert('Pilih pelanggan yang akan diisi saldonya.');
                    return;
                }

                customerTopUpForm.action = customerTopUpForm.action.replace('__CUSTOMER__', customerId);
            });
        }
    </script>
@endsection

@extends('layouts.admin')

@section('title', 'Member NFC')

@section('content')
    <div class="page-heading">
        <h1>Member NFC</h1>
        <p>Informasi member terdaftar dan saldo kartu NFC. Pengelolaan kartu dilakukan dari akun kasir.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <article class="admin-card report-stat-card h-100">
                <div class="admin-card-body">
                    <div class="report-stat-heading">
                        <span class="text-secondary small">Total Member</span>
                        <span class="report-stat-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                    </div>
                    <div class="fs-4 fw-bold mt-2">{{ number_format($memberCount) }}</div>
                </div>
            </article>
        </div>
        <div class="col-md-6">
            <article class="admin-card report-stat-card h-100">
                <div class="admin-card-body">
                    <div class="report-stat-heading">
                        <span class="text-secondary small">Total Saldo Kartu</span>
                        <span class="report-stat-icon"><i class="bi bi-wallet2" aria-hidden="true"></i></span>
                    </div>
                    <div class="fs-4 fw-bold mt-2">Rp {{ number_format((float) $totalBalance, 0, ',', '.') }}</div>
                </div>
            </article>
        </div>
    </div>

    <section class="admin-card">
        <header class="admin-card-header">
            <h2>Daftar Member</h2>
        </header>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Nomor Telepon</th>
                        <th>UID Kartu NFC</th>
                        <th class="text-end">Saldo</th>
                        <th>Aktivitas Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        @php($lastCardTransaction = $member->nfcCard?->transactions->first())
                        <tr>
                            <td class="fw-semibold">{{ $member->name }}</td>
                            <td>{{ $member->phone ?: '—' }}</td>
                            <td><code>{{ $member->nfcCard?->uid ?? '—' }}</code></td>
                            <td class="text-end fw-semibold">
                                {{ $member->nfcCard ? 'Rp '.number_format((float) $member->nfcCard->balance, 0, ',', '.') : '—' }}
                            </td>
                            <td>
                                @if ($lastCardTransaction)
                                    {{ $lastCardTransaction->type === 'top_up' ? 'Isi saldo' : 'Belanja NFC' }}
                                    <div class="small text-muted">
                                        {{ $lastCardTransaction->created_at->format('d/m/Y H:i') }}
                                        · Rp {{ number_format((float) $lastCardTransaction->amount, 0, ',', '.') }}
                                    </div>
                                @else
                                    Belum ada
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Belum ada member NFC yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($members->hasPages())
            <div class="p-3">{{ $members->links() }}</div>
        @endif
    </section>
@endsection

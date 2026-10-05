@extends('layouts.admin')

@section('title', 'Daftar Produk')

@section('content')
    <header class="page-heading">
        <h1>Daftar Produk</h1>
        <p>Lihat harga dan ketersediaan barang untuk transaksi.</p>
    </header>

    <section class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <h2>Produk Tersedia</h2>
            <span class="text-secondary small">{{ $products->count() }} produk</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>{{ $product->category ?? 'Umum' }}</td>
                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                <span class="badge {{ $product->stock > 0 ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $product->stock > 0 ? 'Tersedia' : 'Habis' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-secondary">
                                <i class="bi bi-box-seam d-block mb-2 fs-3" aria-hidden="true"></i>
                                Belum ada produk aktif. Minta admin mengaktifkan barang melalui menu Kelola Barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

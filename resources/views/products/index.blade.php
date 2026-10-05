@extends('layouts.admin')

@section('title', 'Kelola Barang')

@push('styles')
    <style>
        .products-page { min-width: 0; }
        .products-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }
        .products-heading h1 {
            margin: 0;
            color: var(--ink);
            font-size: 25px;
            font-weight: 750;
            letter-spacing: -.03em;
        }
        .products-heading p { margin: 4px 0 0; color: #5f6d82; font-size: 14px; }
        .products-card { overflow: hidden; }
        .products-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 15px 18px;
            border-bottom: 1px solid var(--line);
            background: rgba(248,250,252,.92);
        }
        .products-card-header h2 { margin: 0; color: var(--ink); font-size: 16px; font-weight: 750; }
        .products-count { color: #5f6d82; font-size: 13px; }
        .products-search {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 14px 18px;
            border-bottom: 1px solid var(--line);
            background: rgba(255,255,255,.82);
        }
        .products-search .form-control { max-width: 440px; }
        .products-search .btn { flex: 0 0 auto; }
        .products-search-hint { color: #65748b; font-size: 13px; }
        .products-table-wrap { overflow-x: auto; }
        .products-table { min-width: 760px; }
        .products-table thead th { padding-top: 13px; padding-bottom: 13px; }
        .products-table tbody tr { transition: background-color .15s ease; }
        .products-table tbody tr:hover { background: #f5f9fb; }
        .products-table tbody td { padding-top: 13px; padding-bottom: 13px; }
        .product-cell { display: flex; min-width: 210px; align-items: center; gap: 12px; }
        .product-thumb {
            display: grid;
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            place-items: center;
            overflow: hidden;
            border: 1px solid #e1e8ee;
            border-radius: 9px;
            color: #698091;
            background: #f3f7f9;
            font-size: 18px;
        }
        .product-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .product-name { display: block; color: #25364c; font-size: 14px; font-weight: 700; }
        .product-description { display: block; max-width: 280px; margin-top: 3px; color: #65748b; font-size: 12px; }
        .product-price { color: #07536a; font-weight: 700; white-space: nowrap; }
        .stock-badge {
            display: inline-flex;
            min-width: 42px;
            justify-content: center;
            padding: 5px 9px;
            border-radius: 99px;
            color: #34445b;
            background: #eef2f7;
            font-weight: 700;
        }
        .stock-badge.low { color: #a34b10; background: #fff1df; }
        .product-actions { display: flex; align-items: center; gap: 6px; }
        .product-actions form { margin: 0; }
        .product-action {
            display: inline-flex;
            width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 7px;
            font-size: 14px;
            text-decoration: none;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }
        .product-action.view { color: #087f8c; background: #e3f5f5; }
        .product-action.view:hover { color: #fff; background: #087f8c; }
        .product-action.edit { color: #9a6500; background: #fff3d8; }
        .product-action.edit:hover { color: #fff; background: #a96f00; }
        .product-action.delete { color: #b83c49; background: #fff0ef; }
        .product-action.delete:hover { color: #fff; background: #b83c49; }
        .products-empty { padding: 52px 20px !important; color: #5f6d82 !important; text-align: center; }
        .products-empty i { display: block; margin-bottom: 10px; color: #8da1ae; font-size: 28px; }
        .products-pagination { padding: 16px 18px; border-top: 1px solid var(--line); }
        .products-pagination nav { margin: 0; }
        .products-pagination p { margin-bottom: 0; }
        .products-alert { margin-bottom: 16px; }

        @media (max-width: 575px) {
            .products-heading { align-items: flex-start; flex-direction: column; }
            .products-heading h1 { font-size: 23px; }
            .products-heading .btn { align-self: stretch; }
            .products-card-header { align-items: flex-start; flex-direction: column; }
            .products-search { align-items: stretch; flex-wrap: wrap; }
            .products-search .form-control { max-width: none; flex-basis: 100%; }
            .products-search .btn { flex: 1 1 0; }
        }
    </style>
@endpush

@section('content')
    <div class="products-page">
        <header class="products-heading">
            <div>
                <h1>Kelola Barang</h1>
                <p>Kelola informasi, harga, dan ketersediaan barang toko.</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Barang
            </a>
        </header>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show products-alert" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        <section class="admin-card products-card" aria-labelledby="products-table-title">
            <div class="products-card-header">
                <h2 id="products-table-title">Daftar Barang</h2>
                <span class="products-count">
                    {{ $products->total() }} {{ $search !== '' ? 'hasil pencarian' : 'barang terdaftar' }}
                </span>
            </div>

            <form class="products-search" method="GET" action="{{ route('products.index') }}" role="search">
                <label class="visually-hidden" for="productSearch">Cari barang</label>
                <input
                    class="form-control"
                    id="productSearch"
                    name="search"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Cari nama, kategori, atau deskripsi barang..."
                    maxlength="100"
                >
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search me-1" aria-hidden="true"></i>Cari
                </button>
                @if($search !== '')
                    <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Reset</a>
                @endif
            </form>

            <div class="products-table-wrap">
                <table class="table products-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 64px">No.</th>
                            <th scope="col">Barang</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Stok</th>
                            <th scope="col" style="width: 130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="text-muted">{{ $products->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="product-cell">
                                        <span class="product-thumb">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                            @else
                                                <i class="bi bi-box-seam" aria-hidden="true"></i>
                                            @endif
                                        </span>
                                        <span>
                                            <span class="product-name">{{ $product->name }}</span>
                                            @if($product->description)
                                                <span class="product-description">{{ Str::limit($product->description, 65) }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $product->category ?: 'Tanpa kategori' }}</td>
                                <td class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="stock-badge {{ $product->stock <= 5 ? 'low' : '' }}">{{ $product->stock }}</span>
                                </td>
                                <td>
                                    <div class="product-actions">
                                        <a href="{{ route('products.show', $product) }}" class="product-action view" aria-label="Lihat {{ $product->name }}" title="Lihat detail">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="product-action edit" aria-label="Edit {{ $product->name }}" title="Edit barang">
                                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                        </a>
                                        <button
                                            type="button"
                                            class="product-action delete"
                                            aria-label="Hapus {{ $product->name }}"
                                            title="Hapus barang"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteProductModal"
                                            data-delete-url="{{ route('products.destroy', $product) }}"
                                            data-product-name="{{ $product->name }}"
                                        >
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="products-empty">
                                    <i class="bi bi-inbox" aria-hidden="true"></i>
                                    {{ $search !== '' ? 'Barang tidak ditemukan. Coba kata kunci lain.' : 'Belum ada barang yang terdaftar.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="products-pagination">
                    {{ $products->links() }}
                </div>
            @endif

        </section>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="deleteProductModalTitle">Konfirmasi Hapus Barang</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus barang ini?</p>
                    <strong id="deleteProductName"></strong>
                    <p class="small text-danger mt-3 mb-0">Barang yang sudah dihapus tidak dapat dipulihkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteProductForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash3 me-1" aria-hidden="true"></i>Ya, Hapus Barang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        document.querySelector('#deleteProductModal')?.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;

            if (!(trigger instanceof HTMLElement)) {
                return;
            }

            document.querySelector('#deleteProductForm').action = trigger.dataset.deleteUrl;
            document.querySelector('#deleteProductName').textContent = trigger.dataset.productName;
        });
    </script>
@endpush

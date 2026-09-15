@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Products</h1>
            <p class="text-muted mb-0">
                Daftar produk
            </p>
        </div>

        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i>
            Tambah Product
        </a>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    {{-- Data Product --}}
    <div class="card shadow-sm">

        <div class="card-header">
            <strong>Data Product</strong>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="60">No</th>
                            <th>Product</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($products as $product)

                            <tr>

                                {{-- Nomor --}}
                                <td>
                                    {{ $products->firstItem() + $loop->index }}
                                </td>

                                {{-- Product --}}
                                <td>
                                    <strong>
                                        {{ $product->name }}
                                    </strong>

                                    @if($product->description)
                                        <br>

                                        <small class="text-muted">
                                            {{ Str::limit($product->description, 50) }}
                                        </small>
                                    @endif
                                </td>

                                {{-- Kategori --}}
                                <td>
                                    {{ $product->category ?? '-' }}
                                </td>

                                {{-- Harga --}}
                                <td>
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>

                                {{-- Stock --}}
                                <td>
                                    {{ $product->stock }}
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($product->is_active)

                                        <span class="badge bg-success">
                                            Aktif
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Tidak Aktif
                                        </span>

                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td>

                                    <div class="d-flex gap-1">

                                        {{-- Detail --}}
                                        <a
                                            href="{{ route('products.show', $product) }}"
                                            class="btn btn-sm btn-info text-white">
                                            Detail
                                        </a>

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('products.edit', $product) }}"
                                            class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        {{-- Hapus --}}
                                        <form
                                            action="{{ route('products.destroy', $product) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus product ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    Belum ada data product.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            @endif

        </div>

    </div>

</div>

@endsection

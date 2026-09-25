@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3 mb-0">
            Detail Product
        </h1>

        <a
            href="{{ route('products.index') }}"
            class="btn btn-secondary">
            Kembali
        </a>

    </div>


    {{-- Detail Product --}}
    <div class="card shadow-sm">

        <div class="card-body">

            <table class="table">

                {{-- Gambar --}}
                <tr>
                    <th width="200">
                        Gambar
                    </th>

                    <td>
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 220px; max-height: 220px; object-fit: cover;">
                        @else
                            <span class="text-muted">Tidak ada gambar</span>
                        @endif
                    </td>
                </tr>

                {{-- Nama --}}
                <tr>
                    <th width="200">
                        Nama
                    </th>

                    <td>
                        {{ $product->name }}
                    </td>
                </tr>


                {{-- Kategori --}}
                <tr>
                    <th>
                        Kategori
                    </th>

                    <td>
                        {{ $product->category ?? '-' }}
                    </td>
                </tr>


                {{-- Deskripsi --}}
                <tr>
                    <th>
                        Deskripsi
                    </th>

                    <td>
                        {{ $product->description ?? '-' }}
                    </td>
                </tr>


                {{-- Harga --}}
                <tr>
                    <th>
                        Harga
                    </th>

                    <td>
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                </tr>


                {{-- Stock --}}
                <tr>
                    <th>
                        Stock
                    </th>

                    <td>
                        {{ $product->stock }}
                    </td>
                </tr>


                {{-- Status --}}
                <tr>
                    <th>
                        Status
                    </th>

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
                </tr>


                {{-- Dibuat --}}
                <tr>
                    <th>
                        Dibuat
                    </th>

                    <td>
                        {{ $product->created_at->format('d-m-Y H:i') }}
                    </td>
                </tr>

            </table>

        </div>

    </div>

</div>

@endsection

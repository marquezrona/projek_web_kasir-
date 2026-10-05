{{-- Nama Product --}}
<div class="mb-3">
    <label class="form-label">
        Nama Product
    </label>

    <input
        type="text"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $product->name ?? '') }}"
        placeholder="Masukkan nama product"
    >

    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>


{{-- Kategori --}}
<div class="mb-3">
    <label class="form-label">
        Kategori
    </label>

    <input
        type="text"
        name="category"
        class="form-control @error('category') is-invalid @enderror"
        value="{{ old('category', $product->category ?? '') }}"
        placeholder="Masukkan kategori"
    >

    @error('category')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>


{{-- Deskripsi --}}
<div class="mb-3">
    <label class="form-label">
        Deskripsi
    </label>

    <textarea
        name="description"
        class="form-control @error('description') is-invalid @enderror"
        rows="4"
        placeholder="Masukkan deskripsi product"
    >{{ old('description', $product->description ?? '') }}</textarea>

    @error('description')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>


{{-- Gambar --}}
<div class="mb-3">
    <label class="form-label">
        Gambar Produk
    </label>

    <input
        type="file"
        name="image"
        accept="image/*"
        class="form-control @error('image') is-invalid @enderror"
    >

    @if(!empty($product->image ?? null))
        <div class="mt-2">
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 180px; max-height: 180px; object-fit: cover;">
        </div>
    @endif

    @error('image')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- Status penjualan --}}
<div class="mb-3">
    <input type="hidden" name="is_active" value="0">
    <div class="form-check">
        <input
            type="checkbox"
            name="is_active"
            id="is_active"
            value="1"
            class="form-check-input @error('is_active') is-invalid @enderror"
            @checked(old('is_active', $product->is_active ?? true))
        >
        <label class="form-check-label fw-semibold" for="is_active">
            Aktif dan tampil di transaksi kasir
        </label>
    </div>
    <div class="form-text">
        Barang hanya muncul untuk dijual jika status aktif dan stok lebih dari 0.
    </div>
    @error('is_active')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

{{-- Harga dan Stock --}}
<div class="row">

    {{-- Harga --}}
    <div class="col-md-6">

        <div class="mb-3">

            <label class="form-label">
                Harga
            </label>

            <input
                type="number"
                name="price"
                class="form-control @error('price') is-invalid @enderror"
                value="{{ old('price', $product->price ?? '') }}"
                placeholder="Masukkan harga"
                min="0"
            >

            @error('price')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>


    {{-- Stock --}}
    <div class="col-md-6">

        <div class="mb-3">

            <label class="form-label">
                Stock
            </label>

            <input
                type="number"
                name="stock"
                class="form-control @error('stock') is-invalid @enderror"
                value="{{ old('stock', $product->stock ?? '') }}"
                placeholder="Masukkan stock"
                min="0"
            >

            @error('stock')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</div>

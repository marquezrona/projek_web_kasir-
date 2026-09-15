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

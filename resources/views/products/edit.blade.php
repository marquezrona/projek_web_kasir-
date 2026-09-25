@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-4">
        Edit Product
    </h1>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                action="{{ route('products.update', $product) }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')

                {{-- Form Product --}}
                @include('products.form')

                {{-- Tombol --}}
                <div class="mt-4">

                    <a
                        href="{{ route('products.index') }}"
                        class="btn btn-secondary">
                        Kembali
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Update
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

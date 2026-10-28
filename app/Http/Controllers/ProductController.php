<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    /**
     * Menampilkan semua product.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = trim($validated['search'] ?? '');

        $products = Product::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('category_id', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Menampilkan form tambah product.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Menyimpan product baru dengan manipulasi gambar yang benar.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku'         => 'required|string|max:100|unique:products,sku',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // max 2MB
            'is_active'   => 'nullable|boolean',
        ]);

        $file = $request->file('image');
        
        // Jika ada file unggahan, proses gambar. Jika tidak, lanjutkan tanpa gambar.
        if ($file) {
            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\- _]/', '_', $original) . '.' . $ext;
            $folder = 'products';
            $path = $folder . '/' . $filename;

            $manager = new ImageManager(Driver::class);

            // Perbaikan read() untuk membaca file path fisik
            $image = $manager->read($file->getRealPath())
                             ->cover(800, 800);

            // Perbaikan encoding tanpa error quality pada PNG
            if ($ext === 'png') {
                $encoded = $image->encodeByExtension('png');
            } else {
                $encoded = $image->encodeByExtension('jpg', quality: 85);
            }

            // Simpan ke disk public
            Storage::disk('public')->put($path, $encoded->toString());

            // Sertakan path gambar ke data yang akan disimpan ke DB
            $data['image'] = $path;
        }

        $data['is_active'] = $request->boolean('is_active', true);

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk ditambahkan');
    }

    /**
     * Menampilkan detail product.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Mengupdate product beserta gambar dengan manipulasi baru.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku'         => 'required|string|max:100|unique:products,sku,' . $product->id,
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        $file = $request->file('image');

        if ($file) {
            // Hapus gambar lama dari storage jika ada sebelum menimpanya
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\- _]/', '_', $original) . '.' . $ext;
            $folder = 'products';
            $path = $folder . '/' . $filename;

            $manager = new ImageManager(Driver::class);

            $image = $manager->read($file->getRealPath())
                             ->cover(800, 800);

            if ($ext === 'png') {
                $encoded = $image->encodeByExtension('png');
            } else {
                $encoded = $image->encodeByExtension('jpg', quality: 85);
            }

            Storage::disk('public')->put($path, $encoded->toString());

            $data['image'] = $path;
        }

        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product berhasil diperbarui.');
    }

    /**
     * Menghapus product beserta file gambarnya.
     */
    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product berhasil dihapus.');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_with_an_image(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->post('/products', [
            'name' => 'Beras 5kg',
            'category' => 'Sembako',
            'description' => 'Beras premium',
            'price' => 15000,
            'stock' => 25,
            'is_active' => true,
            'image' => UploadedFile::fake()->image('beras.jpg'),
        ]);

        $response->assertRedirect('/products');

        $product = Product::first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_api_supports_crud_operations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk()->json('token');

        $this->withToken($token);

        $this->getJson('/api/books')
            ->assertOk()
            ->assertExactJson([]);

        $this->postJson('/api/books', [
            'judul' => 'Belajar Laravel',
            'deskripsi' => 'Panduan dasar',
            'status' => 1,
        ])
            ->assertCreated()
            ->assertJsonPath('judul', 'Belajar Laravel')
            ->assertJsonPath('deskripsi', 'Panduan dasar')
            ->assertJsonPath('status', 1);

        $bookId = $this->getJson('/api/books')
            ->assertOk()
            ->json('0.id');

        $this->getJson("/api/books/{$bookId}")
            ->assertOk()
            ->assertJsonPath('judul', 'Belajar Laravel');

        $this->patchJson("/api/books/{$bookId}", ['status' => 0])
            ->assertOk()
            ->assertJsonPath('status', 0);

        $this->deleteJson("/api/books/{$bookId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('buku', ['id' => $bookId]);
    }

    public function test_book_api_requires_authentication(): void
    {
        $this->postJson('/api/books', [
            'judul' => 'Belajar Laravel',
        ])->assertUnauthorized();
    }

    public function test_cashier_cannot_modify_books(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $token = $this->postJson('/api/login', [
            'email' => $cashier->email,
            'password' => 'password',
        ])->assertOk()->json('token');

        $this->withToken($token)
            ->postJson('/api/books', [
                'judul' => 'Belajar Laravel',
            ])
            ->assertForbidden();
    }
}

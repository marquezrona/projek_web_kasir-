<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Book::query()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', 'min:0'],
        ]);

        $book = Book::query()->create($data);

        return response()->json($book, 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(Book::query()->findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'deskripsi' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ]);

        $book = Book::query()->findOrFail($id);
        $book->update($data);

        return response()->json($book->refresh());
    }

    public function destroy(string $id): JsonResponse
    {
        Book::query()->findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with('author');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%$search%')->orWhere('isbn', 'like', '%$search%')->orWhereHas('author', function ($q) use ($search) {
                    $q->where('name', 'like', '%$search%');
                });
            });
        }
        if ($request->filled('genre')) {
            $query->where('genre', 'like', '%search%');
        }

        $book = $query->paginate(10);
        return BookResource::collection($book);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {
        $book = Book::create($request->validated());
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());
        return response()->json([
            new BookResource($book),
            'message' => 'Book updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json([
            new BookResource($book),
            'message' => 'Book deleted successfully',
        ]);
    }
}

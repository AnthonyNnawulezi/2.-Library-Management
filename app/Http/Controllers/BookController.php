<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $book = Book::with('authors', 'isAvailable', 'returnBook', 'borrow')->paginate(10);

if($request->filled('search')){
    $query = Book::with('author');
    $search = $request->search;
    
    $query->where('title', 'like', '%search%')->orWhere('isbn', 'like', '%search%')->orWhereHas('authors', function ($q) use $search{
        $q->where('name', 'like', "%search%");
    });
}
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

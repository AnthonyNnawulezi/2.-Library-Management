<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MemberController;
use App\Models\Author;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', BookController::class);
Route::apiResource('members', MemberController::class);
Route::apiResource('borrowings', BorrowingController::class)->only('index', 'store', 'show');

Route::get('/borrowings/overdue/list', [BorrowingController::class, 'overDue']);
Route::post('/borrowings/{borrowing}/returnbook', [BorrowingController::class, 'returnBook']);

Route::get('statistics', function () {
    return response()->json([
        'Number of Authors' => Author::count(),
        'Number of Books' => Book::count(),
        'Number of Members' => Member::count(),
        'Number of Borrowings' => Borrowing::count(),
        'Overdue Borrowings' => Borrowing::where('status', 'overdue')->count(),
        'Books Borrowed' => Borrowing::where('status', 'borrowed')->count(),
    ]);
});

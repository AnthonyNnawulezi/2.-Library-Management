<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Requests\UpdateBorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Borrowing;
use Illuminate\Http\Request;

use function Illuminate\Support\now;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowings = Borrowing::with('book', 'member')->paginate(12);
        return BorrowingResource::collection($borrowings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request)
    {
        if ($request->status === 'borrowed') {
            return "Book is not available for borrowing";
        }
        $borrowing = Borrowing::create($request->validated());
        $borrowing->load('book', 'member')->borrow();
        return [
            'message' => 'Book borrowed successfully',
            'content' => new BorrowingResource($borrowing)
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load('book', 'member');
        return new BorrowingResource($borrowing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function returnBook(UpdateBorrowingRequest $request, Borrowing $borrowing)
    {
        if ($borrowing->status === 'borrowed') {
            return "Please return your book";
        }
        $borrowing->update($request->validated());
        $borrowing->book()->returnBook();

        return [
            'message' => 'Book returned successfully',
            'content' => new BorrowingResource($borrowing)
        ];
    }

    public function overDue(Borrowing $borrowing)
    {
        $borrowed = Borrowing::where('status', 'borrowed')->where('due_date', '<', now())->get();

        Borrowing::where('status', 'borrowed')->update([
            'status' => 'returned',
        ]);
        return response()->json($borrowed);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Borrowing $request)
    {
        $request->delete();
        return [
            'message' => 'Book deleted successfully',
            'content' => new BorrowingResource($request)
        ];
    }
}

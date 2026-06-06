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
        if (!$request) {
            return "Book is not available for borrowing";
        }
        $borrowing = Borrowing::create($request->validated());
        $borrowing->load('book', 'member');

        $borrowing->book->borrow();
        return [
            'message' => 'Book borrowed successfully',
            'content' => new BorrowingResource($borrowing),
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
    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status == 'returned') {
            return "This book has already been returned";
        }

        $borrowing->update([
            'status' => 'returned',
            'returned_date' => now(),
        ]);

        $borrowing->refresh()->load('book', 'member');
        $borrowing->book->returnBook();

        return new BorrowingResource($borrowing);
    }

    public function overDue()
    {
        Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update([
                'status' => 'overdue',
            ]);

        $borrowed = Borrowing::where('status', 'overdue')->where('due_date', '<', now())->get();

        $borrowed->load('book', 'member');

        // return response()->json($borrowed);
        return BorrowingResource::collection($borrowed);
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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Requests\UpdateBorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Borrowing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laravel\Mcp\Response;

use function Illuminate\Support\now;

// class BorrowingController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         $borrowings = Borrowing::with('book', 'member')->paginate(12);
//         return BorrowingResource::collection($borrowings);
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(StoreBorrowingRequest $request)
//     {
//         if (!$request) {
//             return "Book is not available for borrowing";
//         }
//         $borrowing = Borrowing::create($request->validated());
//         $borrowing->load('book', 'member');

//         $borrowing->book->borrow();
//         return [
//             'message' => 'Book borrowed successfully',
//             'content' => new BorrowingResource($borrowing),
//         ];
//     }

//     /**
//      * Display the specified resource.
//      */
//     public function show(Borrowing $borrowing)
//     {
//         $borrowing->load('book', 'member');
//         return new BorrowingResource($borrowing);
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function returnBook(Borrowing $borrowing)
//     {
//         if ($borrowing->status == 'returned') {
//             return "This book has already been returned";
//         }

//         $borrowing->update([
//             'status' => 'returned',
//             'returned_date' => now(),
//         ]);

//         $borrowing->refresh()->load('book', 'member');
//         $borrowing->book->returnBook();

//         return new BorrowingResource($borrowing);
//     }

//     public function overDue()
//     {
//         Borrowing::where('status', 'borrowed')
//             ->where('due_date', '<', now())
//             ->update([
//                 'status' => 'overdue',
//             ]);

//         $borrowed = Borrowing::where('status', 'overdue')->where('due_date', '<', now())->get();

//         $borrowed->load('book', 'member');

//         // return response()->json($borrowed);
//         return BorrowingResource::collection($borrowed);
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(Borrowing $request)
//     {
//         $request->delete();
//         return [
//             'message' => 'Book deleted successfully',
//             'content' => new BorrowingResource($request)
//         ];
//     }
// }

class BorrowingController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $borrowings = Borrowing::with(['book', 'member'])
            ->latest()
            ->paginate(12);

        return BorrowingResource::collection($borrowings);
    }

    public function store(StoreBorrowingRequest $request): JsonResponse
    {
        $book = $request->book;

        if (! $book->isAvailable()) {
            return response()->json([
                'message' => 'Book is not available for borrowing'
            ], 422);
        }

        $borrowing = Borrowing::create($request->validated());
        $borrowing->load('book', 'member');
        $borrowing->borrow();

        return response()->json([
            'message' => 'Book borrowed successfully',
            'content' => new BorrowingResource($borrowing)
        ], 201);
    }

    public function show(Borrowing $borrowing): BorrowingResource
    {
        return new BorrowingResource($borrowing->load(['book', 'member']));
    }

    public function returnBook(UpdateBorrowingRequest $request, Borrowing $borrowing): JsonResponse
    {
        if ($borrowing->status !== 'borrowed') {
            return response()->json(
                ['message' => 'This borrowing record is not in an active borrowed state.'],
                422
            );
        }

        $borrowing->update($request->validated());
        $borrowing->book->returnBook();

        return response()->json([
            'message'   => 'Book returned successfully.',
            'borrowing' => new BorrowingResource($borrowing->fresh('book', 'member')),
        ]);
    }

    public function overdue(): JsonResponse
    {
        $overdueBorrowings = Borrowing::with('book', 'member')
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->get();

        if ($overdueBorrowings->isEmpty()) {
            return response()->json([
                'message'   => 'No overdue borrowings found.',
                'borrowings' => [],
            ]);
        }

        // Only update the records we fetched, scoped to their IDs
        Borrowing::whereIn('id', $overdueBorrowings->pluck('id'))
            ->update(['status' => 'overdue']);

        return response()->json([
            'message'    => "{$overdueBorrowings->count()} overdue borrowing(s) marked.",
            'borrowings' => BorrowingResource::collection($overdueBorrowings),
        ]);
    }

    public function destroy(Borrowing $borrowing): JsonResponse
    {
        if ($borrowing->status === 'borrowed') {
            return response()->json([
                'message' => 'Cannot delete a borrowing that is still active'
            ], 409);
        }

        $borrowing->delete();

        return response()->json([
            'message' => 'Borrowing record deleted successfully',
            'data'    => new BorrowingResource($borrowing)
        ], 204);
    }
}

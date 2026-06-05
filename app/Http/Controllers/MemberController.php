<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::with('borrowings', 'activeBorrowings')->paginate(10);
        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $member = Member::create($request->validated())->load('borrowings', 'activeBorrowings');
        return response()->json([
            'Success' => 'true',
            'Member' => new MemberResource($member),
            'Message' => 'Member created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        return new MemberResource($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->update($request->validated());
        $member->load('borrowings', 'activeBorrowings');
        return response()->json([
            'Success' => 'true',
            'Member' => new MemberResource($member),
            'Message' => 'Member Updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return response()->noContent();
        // return response()->json([
        //     'Message' => 'Member deleted successfully',
        //     'Success' => true,
        // ]);
    }
}

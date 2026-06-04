<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Members;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Members::with('borrowings', 'activeBorrowings');
        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request, Members $member)
    {
        $member->create($request->validated());
        return response()->json([
            'Success' => 'true',
            'Member' => new MemberResource($member),
            'Message' => 'Member created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Members $member)
    {
        return new MemberResource($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Members $member)
    {
        $member->create($request->validated());
        return response()->json([
            'Success' => 'true',
            'Member' => new MemberResource($member),
            'Message' => 'Member created successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Members $member)
    {
        $member->delete();
        return response()->noContent();
    }
}

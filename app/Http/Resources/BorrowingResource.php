<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'book_id' => $this->book_id,
            'member_id' => $this->member_id,
            'borrowed_date' => $this->borrowed_date->toDateString(),
            'due_date' => $this->due_date->format('Y-M-D'),
            'returned_date' => $this->returned_date?->format('Y-M-D'),
            'status' => $this->status,
            'book' => new BookResource($this->whenLoaded('book')),
            'member' => new MemberResource($this->whenLoaded('member')),
            'is_overdue' => $this->isOverdue(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Members;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'membership_date' => $this->membership_date,
            'status' => $this->status,
            'phone' => $this->phone,
            'active borrowings' => Members::whenLoaded('activeBorrowings')->count()
        ];
    }
}

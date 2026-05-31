<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    /** @use HasFactory<\Database\Factories\MembersFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'address',
        'membership_date',
        'status',
        'phone',
    ];

    protected $casts = [
        'membership_date' => 'date',
        'phone' => 'integer'
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function activeBorrowings()
    {
        return $this->borrowings()->where('status', 'borrowed');
    }
}

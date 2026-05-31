<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'description',
        'author_id',
        'genre',
        'published_at',
        'total_copies',
        'available_copies',
        'cover_image',
        'price',
        'status',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function authors()
    {
        return $this->belongsTo(Author::class);
    }

    public function isAvailable()
    {
        return $this->available_copies > 0;
    }

    public function returnBook()
    {
        if ($this->available_copies < $this->total_copies) {
            return $this->increment('available_copies');
        }
    }

    public function borrow()
    {
        if ($this->available_copies > 0) {
            return $this->decrement('available_copies');
        }
    }
}

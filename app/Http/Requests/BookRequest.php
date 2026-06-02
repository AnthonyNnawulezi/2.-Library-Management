<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            // 'isbn' => ['required', Rule::],
            'isbn' => 'required|string|max:255|unique:books,isbn',
            'description' => 'nullable|string|max:1000',
            'author_id' => 'required|exists:authors,id',
            'genre' => 'string|nullable',
            'published_at' => 'required|date',
            'total_copies' => 'required|integer',
            'available_copies' => 'required|integer',
            'cover_image' => 'nullable|string',
            'price' => 'nullable|decimal:2',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}

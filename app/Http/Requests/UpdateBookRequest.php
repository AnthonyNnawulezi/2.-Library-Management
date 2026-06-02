<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'isbn' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('books', 'isbn')->ignore($this->route('book')),
            ],
            'description' => 'sometimes|nullable|string|max:1000',
            'author_id' => 'sometimes|required|exists:authors,id',
            'genre' => 'sometimes|nullable|string',
            'published_at' => 'sometimes|required|date',
            'total_copies' => 'sometimes|required|integer',
            'available_copies' => 'sometimes|required|integer',
            'cover_image' => 'sometimes|nullable|string',
            'price' => 'sometimes|nullable|decimal:2',
            'status' => 'sometimes|nullable|in:active,inactive',
        ];
    }
}

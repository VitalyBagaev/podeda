<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text'        => 'required|string|min:5|max:1000',
            'category_id' => 'required|exists:categories,id',
            'photo'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'photo2'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'photo3'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }
}

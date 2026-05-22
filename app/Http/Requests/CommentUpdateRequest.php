<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => 'required|string|min:5|max:1000',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'photo2' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'photo3' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }
}

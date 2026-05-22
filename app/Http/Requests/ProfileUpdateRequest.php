<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|min:5|max:255|regex:/^[а-яёА-ЯЁ\s\-]+$/u',
            'email'     => 'required|email|min:5|unique:users,email,' . Auth::id(),
            'phone'     => 'required|string|min:5|max:20|unique:users,phone,' . Auth::id(),
        ];
    }
}

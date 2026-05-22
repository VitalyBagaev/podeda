<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => 'required|string|min:5|max:255',
            'password' => 'required|string|min:5',
        ];
    }
}

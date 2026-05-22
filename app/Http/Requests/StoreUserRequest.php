<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'login' => 'required|string|unique:users,login',
            'full_name' => 'required', 'string', 'regex:/^[А-Яа-яЁё\s]+$/u',
            'phone' => 'required', 'string', 'regex:/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ];
    }
}
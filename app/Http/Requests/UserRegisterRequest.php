<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => 'required|string|min:3|max:255|unique:users,login',
            'full_name' => ['required', 'string', 'min:5', 'max:255', 'regex:/^[А-Яа-яЁё\s]+$/u'],
            'phone' => ['required', 'string', 'regex:/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/'],
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}

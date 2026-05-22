<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnswerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => 'required|string|min:5|max:2000',
        ];
    }
}

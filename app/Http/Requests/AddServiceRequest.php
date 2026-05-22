<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddServiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'service_date' => 'required|date|before_or_equal:today',
        ];
    }
}

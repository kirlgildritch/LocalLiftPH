<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RejectProductRequest extends FormRequest
{
    protected function rejectionKeys(): array
    {
        return [
            'invalid_image',
            'wrong_category',
            'prohibited_item',
            'incomplete_details',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rejection_reason_key' => ['nullable', Rule::in($this->rejectionKeys())],
            'rejection_reason_custom' => ['nullable', 'string', 'max:500'],
        ];
    }
}

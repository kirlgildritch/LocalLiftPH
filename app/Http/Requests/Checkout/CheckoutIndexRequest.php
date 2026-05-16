<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'selected_cart_items' => ['nullable', 'array'],
            'selected_cart_items.*' => ['integer'],
            'voucher_code' => ['nullable', 'string', 'max:50'],
        ];
    }
}

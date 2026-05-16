<?php

namespace App\Http\Requests\Checkout;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutStoreRequest extends FormRequest
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
            'payment_method' => ['required', Rule::in(array_keys(Order::paymentMethods()))],
            'voucher_code' => ['nullable', 'string', 'max:50'],
        ];
    }
}

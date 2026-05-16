<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function getRedirectUrl(): string
    {
        return route('seller.settings') . '#inventory';
    }

    public function rules(): array
    {
        return [
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:9999'],
            'hide_out_of_stock' => ['nullable', 'boolean'],
        ];
    }
}

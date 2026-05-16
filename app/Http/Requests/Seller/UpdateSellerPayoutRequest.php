<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerPayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function getRedirectUrl(): string
    {
        return route('seller.settings') . '#payout';
    }

    public function rules(): array
    {
        return [
            'payout_method' => ['required', 'string', 'max:50'],
            'payout_account_name' => ['required', 'string', 'max:255'],
            'payout_account_number' => ['required', 'string', 'max:100'],
        ];
    }
}

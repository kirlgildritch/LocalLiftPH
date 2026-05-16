<?php

namespace App\Http\Requests\Seller;

use App\Models\Seller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateSellerStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function getRedirectUrl(): string
    {
        return route('seller.settings') . '#status';
    }

    public function rules(): array
    {
        return [
            'shop_status' => ['required', 'string', 'in:open,temporarily_closed,vacation'],
            'shop_status_until' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (
                ($this->input('shop_status') ?? null) === Seller::SHOP_STATUS_TEMPORARILY_CLOSED
                && empty($this->input('shop_status_until'))
            ) {
                $validator->errors()->add('shop_status_until', 'Select an until date.');
            }
        });
    }
}

<?php

namespace App\Http\Requests\Seller;

use App\Models\Voucher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $voucher = $this->route('voucher');

        return [
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('vouchers', 'code')->ignore($voucher?->id)],
            'name' => ['nullable', 'string', 'max:120'],
            'type' => ['required', Rule::in([Voucher::TYPE_FIXED, Voucher::TYPE_PERCENT])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'minimum_subtotal' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount' => ['nullable', 'numeric', 'min:0.01'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

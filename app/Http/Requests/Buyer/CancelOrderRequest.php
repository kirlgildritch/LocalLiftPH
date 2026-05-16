<?php

namespace App\Http\Requests\Buyer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reasons' => ['required', 'array', 'min:1'],
            'reasons.*' => ['string', 'in:Changed my mind,Item price too high,Found better price elsewhere,Item damaged / defective,Delivery delay,Other'],
            'other_reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $reasons = collect($this->input('reasons', []));
            $otherReason = trim((string) $this->input('other_reason', ''));

            if ($reasons->contains('Other') && $otherReason === '') {
                $validator->errors()->add('other_reason', 'Provide a custom reason when selecting Other.');
            }
        });
    }
}

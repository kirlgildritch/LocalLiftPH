<?php

namespace App\Http\Requests\Buyer;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'in:Damaged item,Wrong item received,Missing item,Item not as described,Quality issue,Other'],
            'preferred_resolution' => ['required', 'string', 'in:refund,return_and_refund,replacement'],
            'details' => ['required', 'string', 'min:10', 'max:1000'],
            'evidence' => ['nullable', 'array', 'max:5'],
            'evidence.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,avi,webm', 'max:10240'],
        ];
    }
}

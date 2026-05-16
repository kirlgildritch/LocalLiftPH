<?php

namespace App\Http\Requests\Seller;

use App\Models\OrderReturnRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondToOrderReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                OrderReturnRequest::STATUS_APPROVED,
                OrderReturnRequest::STATUS_REJECTED,
            ])],
            'seller_response' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }
}

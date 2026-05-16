<?php

namespace App\Http\Requests\Admin;

use App\Models\Seller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewSellerApplicationRequest extends FormRequest
{
    protected function documentRequestReasons(): array
    {
        return [
            'proof_of_address',
            'tax_identification_number',
            'bank_statement',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $requestMoreDocuments = $this->boolean('request_more_documents');

        return [
            'application_status' => ['required', Rule::in([Seller::STATUS_PENDING, Seller::STATUS_APPROVED, Seller::STATUS_REJECTED])],
            'review_notes' => ['nullable', 'string', 'max:1000'],
            'document_request_reason' => [
                Rule::requiredIf($requestMoreDocuments),
                'nullable',
                Rule::in($this->documentRequestReasons()),
            ],
        ];
    }
}

<?php

namespace App\Http\Requests\Seller;

use App\Models\Seller;
use App\Models\SellerDocumentRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitSellerApplicationRequest extends FormRequest
{
    protected ?Seller $existingSeller = null;

    protected ?SellerDocumentRequest $latestDocumentRequest = null;

    protected function prepareForValidation(): void
    {
        $user = $this->user('seller');

        $this->existingSeller = $user
            ? Seller::with('latestDocumentRequest')->where('user_id', $user->id)->first()
            : null;
        $this->latestDocumentRequest = $this->existingSeller?->latestDocumentRequest;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $existingSeller = $this->existingSeller;
        $latestDocumentRequest = $this->latestDocumentRequest;
        $needsResubmission = $latestDocumentRequest?->status === SellerDocumentRequest::STATUS_PENDING;

        return [
            'seller_type' => ['required', Rule::in(['individual', 'registered_business'])],
            'full_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'valid_id_type' => ['required', 'string', 'max:100'],
            'valid_id_number' => ['required', 'string', 'max:120'],
            'valid_id_document' => [
                Rule::requiredIf(! $existingSeller?->valid_id_path),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,webp',
                'max:4096',
            ],
            'business_permit' => [
                Rule::requiredIf(
                    $this->input('seller_type') === 'registered_business'
                    && ! $existingSeller?->business_permit_path
                ),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,webp',
                'max:4096',
            ],
            'requested_document' => [
                Rule::requiredIf($needsResubmission),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,webp',
                'max:4096',
            ],
            'street_address' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'landmark' => ['required', 'string', 'max:255'],
        ];
    }
}

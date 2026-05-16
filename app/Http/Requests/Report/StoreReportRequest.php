<?php

namespace App\Http\Requests\Report;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReportRequest extends FormRequest
{
    protected $errorBag = 'reportSubmission';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'seller_id' => ['nullable', 'integer', 'exists:users,id'],
            'reason' => ['required', Rule::in(['spam', 'fake product', 'inappropriate', 'other'])],
            'message' => ['nullable', 'string', 'max:1500'],
            'modal_context' => ['nullable', Rule::in(['product', 'seller'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('product_id') && ! $this->filled('seller_id')) {
                $validator->errors()->add('reason', 'Please choose a valid product or seller to report.');
                return;
            }

            $product = null;
            if ($this->filled('product_id')) {
                $product = Product::query()->find($this->integer('product_id'));
            }

            if ($product && $this->filled('seller_id') && (int) $product->user_id !== (int) $this->integer('seller_id')) {
                $validator->errors()->add('seller_id', 'The selected seller does not match the reported product.');
            }

            if ($this->filled('seller_id')) {
                $seller = User::query()->find($this->integer('seller_id'));

                if (! $seller || ! $seller->isSeller()) {
                    $validator->errors()->add('seller_id', 'The selected seller is invalid.');
                }
            }
        });
    }

    protected function failedValidation(ValidatorContract $validator): void
    {
        $response = redirect($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($validator, $this->errorBag)
            ->with('report_modal_open', $this->input('modal_context', 'product'));

        throw new HttpResponseException($response);
    }
}

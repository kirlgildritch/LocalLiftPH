<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

abstract class SellerProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function variantValidationRules(): array
    {
        return [
            'has_variants' => 'nullable|boolean',
            'variants' => 'nullable|array|max:60',
            'variants.*.id' => 'nullable|integer',
            'variants.*.name' => 'nullable|string|max:120',
            'variants.*.sku' => 'nullable|string|max:80',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.is_active' => 'nullable|boolean',
            'variants.*.image' => 'nullable|image|max:51200',
        ];
    }

    protected function mediaValidationRules(): array
    {
        return [
            'image' => 'nullable|image|max:51200',
            'media' => 'nullable|array|max:12',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv,3gp,m4v|max:51200',
        ];
    }
}

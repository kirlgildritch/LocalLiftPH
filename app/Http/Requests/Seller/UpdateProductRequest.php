<?php

namespace App\Http\Requests\Seller;

class UpdateProductRequest extends SellerProductRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|in:new,used',
            'description' => 'required|string',
            'weight' => 'required|numeric|min:0.01',
            'width_cm' => 'required|numeric|min:0.01',
            'length_cm' => 'required|numeric|min:0.01',
            'height_cm' => 'required|numeric|min:0.01',
        ] + $this->discountValidationRules() + $this->mediaValidationRules() + $this->variantValidationRules();
    }
}

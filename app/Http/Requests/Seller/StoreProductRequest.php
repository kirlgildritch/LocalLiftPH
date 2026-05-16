<?php

namespace App\Http\Requests\Seller;

class StoreProductRequest extends SellerProductRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'condition' => 'required|in:new,used',
            'description' => 'required|string',
            'weight' => 'required|numeric|min:0.01',
            'width_cm' => 'required|numeric|min:0.01',
            'length_cm' => 'required|numeric|min:0.01',
            'height_cm' => 'required|numeric|min:0.01',
        ] + $this->mediaValidationRules() + $this->variantValidationRules();
    }
}

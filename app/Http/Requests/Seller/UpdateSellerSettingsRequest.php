<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string|max:2000',
            'contact_number' => 'required|string|max:20',
            'shop_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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

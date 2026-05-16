<?php

namespace App\Http\Requests\Seller;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateOrderShippingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_status' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $order = $this->route('order');

            if (! $order instanceof Order) {
                return;
            }

            $allowedStatuses = $order->nextShippingStatuses();
            $shippingStatus = (string) $this->input('shipping_status', '');

            if ($allowedStatuses === [] || ! in_array($shippingStatus, $allowedStatuses, true)) {
                $validator->errors()->add('shipping_status', 'Invalid shipping status transition.');
            }
        });
    }
}

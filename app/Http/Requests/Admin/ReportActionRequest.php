<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in([
                'warn_seller',
                'delist_product',
                'ban_product',
                'suspend_seller',
                'mark_resolved',
                'dismiss_report',
            ])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

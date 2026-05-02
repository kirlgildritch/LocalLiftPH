<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use App\Notifications\AdminActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'seller_id' => ['nullable', 'integer', 'exists:users,id'],
            'reason' => ['required', Rule::in(['spam', 'fake product', 'inappropriate', 'other'])],
            'message' => ['nullable', 'string', 'max:1500'],
            'modal_context' => ['nullable', Rule::in(['product', 'seller'])],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (! $request->filled('product_id') && ! $request->filled('seller_id')) {
                $validator->errors()->add('reason', 'Please choose a valid product or seller to report.');
                return;
            }

            $product = null;
            if ($request->filled('product_id')) {
                $product = Product::query()->find($request->integer('product_id'));
            }

            if ($product && $request->filled('seller_id') && (int) $product->user_id !== (int) $request->integer('seller_id')) {
                $validator->errors()->add('seller_id', 'The selected seller does not match the reported product.');
            }

            if ($request->filled('seller_id')) {
                $seller = User::query()->find($request->integer('seller_id'));
                if (! $seller || ! $seller->isSeller()) {
                    $validator->errors()->add('seller_id', 'The selected seller is invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'reportSubmission')
                ->withInput()
                ->with('report_modal_open', $request->input('modal_context', 'product'));
        }

        $report = Report::create([
            'user_id' => (int) $request->user()->id,
            'product_id' => $request->integer('product_id') ?: null,
            'seller_id' => $request->integer('seller_id') ?: null,
            'reason' => $request->input('reason'),
            'message' => trim((string) $request->input('message')) ?: null,
            'status' => Report::STATUS_PENDING,
        ]);

        $targetLabel = $report->product_id
            ? (Product::query()->find($report->product_id)?->name ?: 'a product')
            : (User::query()->find($report->seller_id)?->name ?: 'a seller');

        $this->notifyAdmins(
            new AdminActivityNotification(
                'reports',
                'New report submitted',
                ($request->user()->name ?? 'A buyer') . ' reported ' . $targetLabel . '.',
                'admin.reports',
            )
        );

        return back()->with('success', 'Your report has been submitted for review.');
    }

    private function notifyAdmins(AdminActivityNotification $notification): void
    {
        User::query()
            ->where(function ($query) {
                $query->where('is_admin', true)
                    ->orWhere('role', 'admin');
            })
            ->get()
            ->each
            ->notify($notification);
    }
}

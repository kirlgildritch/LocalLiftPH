<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReturnRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderReturnRequestController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('view', $order);
        $order->loadMissing(['items.product', 'returnRequest']);

        if (! $order->canRequestReturnRefund()) {
            return redirect()
                ->route('buyer.orders.show', $order)
                ->with('error', 'Return/refund requests are available for completed orders within 7 days.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:Damaged item,Wrong item received,Missing item,Item not as described,Quality issue,Other'],
            'preferred_resolution' => ['required', 'string', 'in:refund,return_and_refund,replacement'],
            'details' => ['required', 'string', 'min:10', 'max:1000'],
            'evidence' => ['nullable', 'array', 'max:5'],
            'evidence.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,avi,webm', 'max:10240'],
        ]);

        $returnRequest = OrderReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'seller_id' => $order->seller_id,
            'reason' => $validated['reason'],
            'preferred_resolution' => $validated['preferred_resolution'],
            'details' => $validated['details'],
            'status' => OrderReturnRequest::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        foreach ($request->file('evidence', []) as $file) {
            $path = $file->store('return-requests', 'public');
            $mimeType = (string) $file->getMimeType();

            $returnRequest->media()->create([
                'type' => Str::startsWith($mimeType, 'video/') ? 'video' : 'image',
                'path' => $path,
            ]);
        }

        return redirect()
            ->route('buyer.orders.show', $order)
            ->with('success', 'Return/refund request submitted. The seller can now review it.');
    }
}

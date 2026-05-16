<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\AdminActivityNotification;

class AdminActivityService
{
    public function sellerProfileUpdated(User $user, array $changedFields): void
    {
        $this->notify(
            'seller_review',
            'Seller profile updated',
            ($user->name ?? 'A seller') . ' updated their seller profile: ' . $this->formatFieldList($changedFields) . '.',
            'admin.sellers',
        );
    }

    public function sellerShopSettingsUpdated(Seller $seller, array $changedFields): void
    {
        $this->notify(
            'seller_review',
            'Seller shop settings updated',
            $this->sellerDisplayName($seller) . ' updated shop settings: ' . $this->formatFieldList($changedFields) . '.',
            'admin.sellers',
        );
    }

    public function sellerPayoutUpdated(Seller $seller, array $changedFields): void
    {
        $this->notify(
            'seller_review',
            'Seller payout details updated',
            $this->sellerDisplayName($seller) . ' updated payout details: ' . $this->formatFieldList($changedFields) . '.',
            'admin.payouts',
        );
    }

    public function sellerInventoryUpdated(Seller $seller, array $changedFields): void
    {
        $this->notify(
            'seller_review',
            'Seller inventory settings updated',
            $this->sellerDisplayName($seller) . ' updated inventory settings: ' . $this->formatFieldList($changedFields) . '.',
            'admin.sellers',
        );
    }

    public function sellerStatusUpdated(Seller $seller, string $statusMessage): void
    {
        $this->notify(
            'seller_review',
            'Seller shop status updated',
            $this->sellerDisplayName($seller) . ' changed shop status to ' . $statusMessage . '.',
            'admin.sellers',
        );
    }

    public function sellerApplicationSubmitted(?Seller $seller, string $fallbackName, bool $isResubmission): void
    {
        $sellerName = $seller?->store_name ?: $fallbackName;

        $this->notify(
            'seller_review',
            $isResubmission ? 'Seller documents resubmitted' : 'New seller application',
            $isResubmission
                ? ($sellerName . ' uploaded the requested verification documents.')
                : ($sellerName . ' submitted a seller application for review.'),
            'admin.sellers',
        );
    }

    public function productSubmitted(string $productName, string $sellerName): void
    {
        $this->notify(
            'products',
            'New product awaiting approval',
            $productName . ' was submitted by ' . $sellerName . ' for review.',
            'admin.products',
        );
    }

    public function productUpdated(string $originalName, string $updatedName, string $sellerName, array $changedFields): void
    {
        $message = $originalName !== $updatedName
            ? $originalName . ' was updated by ' . $sellerName . ' and renamed to ' . $updatedName
            : $updatedName . ' was updated by ' . $sellerName;

        $message .= '. Changed: ' . $this->formatFieldList($changedFields) . '.';

        $this->notify(
            'products',
            'Product updated by seller',
            $message,
            'admin.products',
        );
    }

    public function productDeleted(string $productName, string $sellerName): void
    {
        $this->notify(
            'products',
            'Product deleted by seller',
            $productName . ' was deleted by ' . $sellerName . '.',
            'admin.products',
        );
    }

    public function reportSubmitted(User $reporter, string $targetLabel): void
    {
        $this->notify(
            'reports',
            'New report submitted',
            ($reporter->name ?? 'A buyer') . ' reported ' . $targetLabel . '.',
            'admin.reports',
        );
    }

    public function notify(string $type, string $title, string $message, string $route): void
    {
        $notification = new AdminActivityNotification($type, $title, $message, $route);

        User::query()
            ->where(function ($query) {
                $query->where('is_admin', true)
                    ->orWhere('role', 'admin');
            })
            ->get()
            ->each
            ->notify($notification);
    }

    public function formatFieldList(array $fields): string
    {
        $fields = array_values(array_unique($fields));
        $count = count($fields);

        if ($count === 0) {
            return 'details';
        }

        if ($count === 1) {
            return $fields[0];
        }

        if ($count === 2) {
            return $fields[0] . ' and ' . $fields[1];
        }

        $lastField = array_pop($fields);

        return implode(', ', $fields) . ', and ' . $lastField;
    }

    private function sellerDisplayName(Seller $seller): string
    {
        return $seller->store_name ?: ($seller->user?->name ?? 'A seller');
    }
}

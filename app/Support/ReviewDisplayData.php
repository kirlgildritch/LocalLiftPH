<?php

namespace App\Support;

use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class ReviewDisplayData
{
    public function __construct(
        public readonly Review $review,
        public readonly Collection $media,
        public readonly ?string $purchaseDetails,
        public readonly Fluent $sellerReply,
    ) {
    }

    public static function forSellerReview(Review $review): self
    {
        return new self(
            review: $review,
            media: self::displayMedia($review),
            purchaseDetails: $review->purchaseDetailsLabel(),
            sellerReply: self::sellerReplyState($review),
        );
    }

    private static function displayMedia(Review $review): Collection
    {
        if ($review->relationLoaded('media') && $review->media->isNotEmpty()) {
            return $review->media;
        }

        return collect([
            $review->image_path ? (object) ['type' => 'image', 'path' => $review->image_path] : null,
            $review->video_path ? (object) ['type' => 'video', 'path' => $review->video_path] : null,
        ])->filter()->values();
    }

    private static function sellerReplyState(Review $review): Fluent
    {
        $hasReply = filled($review->seller_reply);

        return new Fluent([
            'hasReply' => $hasReply,
            'statusLabel' => $hasReply ? 'Public reply posted' : 'No public reply yet',
            'statusTone' => $hasReply ? 'posted' : 'empty',
            'formTitle' => $hasReply ? 'Edit public reply' : 'Write a public reply',
            'formHint' => $hasReply
                ? 'Keep edits short, helpful, and consistent with what future buyers should see.'
                : 'Reply professionally. Future buyers can read this under the review.',
            'buttonLabel' => $hasReply ? 'Update Reply' : 'Post Reply',
            'placeholder' => 'Thank the buyer, answer concerns, or clarify product details in a helpful tone...',
        ]);
    }
}

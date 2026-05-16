<?php

namespace App\Http\Requests\ProductReview;

use App\Support\ReviewUploadLimit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeFileInput('review_media');
        $this->normalizeFileInput('review_image');
        $this->normalizeFileInput('review_video');
    }

    public function rules(): array
    {
        $maxFiles = ReviewUploadLimit::maxFiles();
        $maxFileKilobytes = ReviewUploadLimit::appMaxFileKilobytes();

        return [
            'order_item_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1500'],
            'review_media' => ['nullable', 'array', 'max:' . $maxFiles],
            'review_media.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv,3gp,m4v', 'max:' . $maxFileKilobytes],
            'review_image' => ['nullable', 'array', 'max:' . $maxFiles],
            'review_image.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv,3gp,m4v', 'max:' . $maxFileKilobytes],
            'review_video' => ['nullable', 'array', 'max:' . $maxFiles],
            'review_video.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv,3gp,m4v', 'max:' . $maxFileKilobytes],
        ];
    }

    private function normalizeFileInput(string $key): void
    {
        if (! $this->hasFile($key)) {
            return;
        }

        $files = $this->file($key);

        if ($files instanceof UploadedFile) {
            $this->files->set($key, [$files]);
        }
    }
}

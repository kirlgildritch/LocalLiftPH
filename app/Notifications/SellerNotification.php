<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SellerNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $category,
        protected string $action,
        protected string $title,
        protected string $message,
        protected ?string $route = null,
        protected array $routeParams = [],
        protected ?string $url = null,
        protected ?string $relatedType = null,
        protected ?int $relatedId = null,
        protected ?string $dedupeKey = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    public function broadcastWith(): array
    {
        return array_merge($this->payload(), [
            'id' => $this->id,
            'read_at' => null,
            'created_at_human' => 'Just now',
        ]);
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage($this->broadcastWith()))
            ->onConnection('sync');
    }

    private function payload(): array
    {
        return [
            'type' => $this->category,
            'category' => $this->category,
            'action' => $this->action,
            'title' => $this->title,
            'message' => $this->message,
            'route' => $this->route,
            'route_params' => $this->routeParams,
            'url' => $this->url,
            'related_type' => $this->relatedType,
            'related_id' => $this->relatedId,
            'dedupe_key' => $this->dedupeKey,
        ];
    }
}

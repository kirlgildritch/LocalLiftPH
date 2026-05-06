<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdminActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $type,
        protected string $title,
        protected string $message,
        protected ?string $route = null,
        protected array $routeParams = [],
        protected ?string $url = null,
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
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'route' => $this->route,
            'route_params' => $this->routeParams,
            'url' => $this->url,
        ];
    }
}

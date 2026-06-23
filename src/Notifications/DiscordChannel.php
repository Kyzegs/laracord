<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Notifications;

use Illuminate\Notifications\Notification;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\LaracordManager;
use Kyzegs\Laracord\Payloads\DiscordMessage;
use Kyzegs\Laracord\ValueObjects\DiscordWebhook;

final readonly class DiscordChannel
{
    public function __construct(private LaracordManager $laracordManager) {}

    public function send(object $notifiable, Notification $notification): ?DiscordResponse
    {
        if (! method_exists($notifiable, 'routeNotificationFor')) {
            throw new \UnexpectedValueException('Discord notifiables must define routeNotificationFor().');
        }

        $route = $notifiable->routeNotificationFor('discord', $notification);
        if ($route === null || $route === '') {
            return null;
        }

        $webhook = $route instanceof DiscordWebhook ? $route : DiscordWebhook::fromUrl((string) $route);
        if (! method_exists($notification, 'toDiscord')) {
            throw new \UnexpectedValueException('Discord notifications must define toDiscord().');
        }

        $message = $notification->toDiscord($notifiable);
        if (! $message instanceof DiscordMessage) {
            throw new \UnexpectedValueException('toDiscord() must return DiscordMessage.');
        }

        return $this->laracordManager->withoutAuthentication()->resource('webhooks')->call(
            'execute',
            ['webhook_id' => $webhook->id, 'webhook_token' => $webhook->token()],
            $message,
            ['wait' => true],
            $message->files(),
        );
    }
}

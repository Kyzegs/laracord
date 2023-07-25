<?php

namespace Kyzegs\Laracord\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Kyzegs\Laracord\Client\Http;

class DiscordChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     */
    public function send($notifiable, Notification $notification): mixed
    {
        if (! $channelId = $notifiable->routeNotificationFor('discord', $notification)) {
            return null;
        }

        $message = $notification->toDiscord($notifiable);

        $route = 'users/@me/channels';

        // TODO: Come up with cache key naming convention
        // TODO: Move all API calls to their own location

        if ($message->private) {
            $channelId = Cache::rememberForever(sprintf('%s:%d', $route, $channelId), function () use ($route, $channelId) {
                return Http::post($route, ['recipient_id' => $channelId])->json('id');
            });
        }

        return Http::post(sprintf('channels/%s/messages', $channelId), $message->toArray())->json();
    }
}

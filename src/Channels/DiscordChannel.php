<?php

namespace Kyzegs\Laracord\Channels;

use Illuminate\Notifications\Notification;
use Kyzegs\Laracord\Facades\Laracord;

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

        if ($message->private) {
            $channelId = Laracord::createDm(['recipient_id' => $channelId])['id'];
        }

        return Laracord::createMessage($channelId, $message->toArray());
    }
}

# Webhook Notifications

```php
use Kyzegs\Laracord\Notifications\DiscordChannel;
use Kyzegs\Laracord\Payloads\DiscordMessage;

public function via(object $notifiable): array
{
    return [DiscordChannel::class];
}

public function toDiscord(object $notifiable): DiscordMessage
{
    return (new DiscordMessage)->content('Invoice paid');
}
```

`routeNotificationForDiscord()` should return a Discord webhook URL or `DiscordWebhook`. On-demand routing through channel name `discord` is also registered. Webhook requests default to `wait=true` so delivery failures surface.

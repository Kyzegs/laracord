<?php

declare(strict_types=1);

use Illuminate\Notifications\Notification;
use Kyzegs\Laracord\Components\TextDisplay;
use Kyzegs\Laracord\Facades\Laracord;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Notifications\DiscordChannel;
use Kyzegs\Laracord\Payloads\DiscordMessage;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

function discordNotification(DiscordMessage $message): Notification
{
    return new class($message) extends Notification
    {
        public function __construct(private readonly DiscordMessage $message) {}

        public function toDiscord(object $notifiable): DiscordMessage
        {
            return $this->message;
        }
    };
}

function discordNotifiable(?string $route = 'https://discord.com/api/webhooks/123456789012345678/secret-token'): object
{
    return new readonly class($route)
    {
        public function __construct(private ?string $route) {}

        public function routeNotificationFor(string $driver, Notification $notification): ?string
        {
            return $this->route;
        }
    };
}

it('sends through the webhook named by the notifiable', function (): void {
    $fake = Laracord::fake();

    resolve(DiscordChannel::class)->send(discordNotifiable(), discordNotification((new DiscordMessage)->content('Hi')));

    $fake->assertSent('webhooks', 'execute', fn (DiscordRequest $request): bool => $request->parameters['webhook_id'] === '123456789012345678'
        && $request->parameters['webhook_token'] === 'secret-token'
        && $request->query === ['wait' => true]);
});

it('asks Discord to honour components, which it otherwise ignores for webhooks', function (): void {
    $fake = Laracord::fake();

    resolve(DiscordChannel::class)->send(discordNotifiable(), discordNotification((new DiscordMessage)->componentsV2([TextDisplay::make('Hi')])));

    $fake->assertSent('webhooks', 'execute', fn (DiscordRequest $request): bool => ($request->query['with_components'] ?? false) === true);
});

it('uploads files attached to the message', function (): void {
    $fake = Laracord::fake();

    resolve(DiscordChannel::class)->send(discordNotifiable(), discordNotification((new DiscordMessage)->file('data', 'a.txt')));

    $fake->assertSent('webhooks', 'execute', fn (DiscordRequest $request): bool => count($request->files) === 1);
});

it('does nothing without a route', function (): void {
    $fake = Laracord::fake();

    $result = resolve(DiscordChannel::class)->send(discordNotifiable(null), discordNotification((new DiscordMessage)->content('Hi')));

    expect($result)->toBeNull();
    $fake->assertNothingSent();
});

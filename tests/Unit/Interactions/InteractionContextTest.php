<?php

declare(strict_types=1);

use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Facades\Laracord;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Interactions\Interaction;
use Kyzegs\Laracord\Payloads\DiscordMessage;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

function interactionSnowflake(DateTimeImmutable $createdAt): string
{
    $milliseconds = ((int) $createdAt->format('U')) * 1000;

    return (string) (($milliseconds - 1_420_070_400_000) << 22);
}

function activeInteraction(): Interaction
{
    return new Interaction([
        'id' => interactionSnowflake(new DateTimeImmutable('now', new DateTimeZone('UTC'))),
        'application_id' => 'app-1',
        'token' => 'interaction-token',
        'type' => 2,
    ]);
}

it('handles the original response and followup lifecycle', function (): void {
    $fake = Laracord::fake([
        'interactions.*' => Laracord::response(['id' => 'message-1']),
    ]);
    $context = activeInteraction()->context(Laracord::withoutAuthentication());

    $context->getOriginal();
    $context->editOriginal((new DiscordMessage)->content('edited')->file('contents', 'note.txt'));
    $context->deleteOriginal();
    $context->followup(['content' => 'later']);
    $context->getFollowup('message-1');
    $context->editFollowup('message-1', ['content' => 'updated']);
    $context->deleteFollowup('message-1');

    $fake->assertSentCount(7);
    $fake->assertSent('interactions', 'getOriginal', static fn (DiscordRequest $request): bool => $request->authentication === AuthenticationRequirement::NONE
        && $request->resolvedPath() === '/webhooks/app-1/interaction-token/messages/@original');
    $fake->assertSent('interactions', 'editOriginal', static fn (DiscordRequest $request): bool => ($request->files[0]['filename'] ?? null) === 'note.txt');
    $fake->assertSent('interactions', 'createFollowup', static fn (DiscordRequest $request): bool => $request->query === ['wait' => true]);
    $fake->assertSent('interactions', 'getFollowup', static fn (DiscordRequest $request): bool => $request->resolvedPath() === '/webhooks/app-1/interaction-token/messages/message-1');

    expect(array_map(static fn (DiscordRequest $request): ?string => $request->endpoint, $fake->recorded()))
        ->toBe(['getOriginal', 'editOriginal', 'deleteOriginal', 'createFollowup', 'getFollowup', 'editFollowup', 'deleteFollowup']);
});

it('rejects lifecycle calls after the interaction token expires', function (): void {
    $fake = Laracord::fake();
    $interaction = new Interaction([
        'id' => interactionSnowflake(new DateTimeImmutable('-16 minutes', new DateTimeZone('UTC'))),
        'application_id' => 'app-1',
        'token' => 'expired-token',
    ]);

    expect(fn () => $interaction->context(Laracord::withoutAuthentication())->getOriginal())
        ->toThrow(LogicException::class, 'Interaction token expired after 15 minutes.');

    $fake->assertNothingSent();
});

it('reads nested options and all resolved object maps', function (): void {
    $interaction = new Interaction(['data' => [
        'options' => [[
            'name' => 'admin',
            'type' => 2,
            'options' => [[
                'name' => 'ban',
                'type' => 1,
                'options' => [['name' => 'user', 'type' => 6, 'value' => 'user-1']],
            ]],
        ]],
        'resolved' => [
            'users' => ['user-1' => ['id' => 'user-1']],
            'members' => ['user-1' => ['roles' => []]],
            'roles' => ['role-1' => ['id' => 'role-1']],
            'channels' => ['channel-1' => ['id' => 'channel-1']],
            'messages' => ['message-1' => ['id' => 'message-1']],
            'attachments' => ['attachment-1' => ['id' => 'attachment-1']],
        ],
    ]]);

    expect($interaction->option('admin.ban.user'))->toBe('user-1')
        ->and($interaction->optionData('admin.ban'))->toMatchArray(['type' => 1])
        ->and($interaction->resolvedUsers())->toHaveKey('user-1')
        ->and($interaction->resolvedMembers())->toHaveKey('user-1')
        ->and($interaction->resolvedRoles())->toHaveKey('role-1')
        ->and($interaction->resolvedChannels())->toHaveKey('channel-1')
        ->and($interaction->resolvedMessages())->toHaveKey('message-1')
        ->and($interaction->resolvedAttachments())->toHaveKey('attachment-1')
        ->and($interaction->resolved('users', 'user-1'))->toBe(['id' => 'user-1']);
});

it('derives the fifteen minute token lifetime from the interaction snowflake', function (): void {
    $createdAt = new DateTimeImmutable('2026-07-20 10:00:00', new DateTimeZone('UTC'));
    $interaction = new Interaction(['id' => interactionSnowflake($createdAt)]);

    expect($interaction->createdAt()->format('Y-m-d H:i:s'))->toBe('2026-07-20 10:00:00')
        ->and($interaction->expiresAt()->format('Y-m-d H:i:s'))->toBe('2026-07-20 10:15:00')
        ->and($interaction->isExpired(new DateTimeImmutable('2026-07-20 10:14:59', new DateTimeZone('UTC'))))->toBeFalse()
        ->and($interaction->isExpired(new DateTimeImmutable('2026-07-20 10:15:00', new DateTimeZone('UTC'))))->toBeTrue();
});

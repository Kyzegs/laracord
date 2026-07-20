<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Enums\HttpMethod;
use Kyzegs\Laracord\Events\ResponseReceived;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Tests\TestCase;
use Laravel\Telescope\ExtractProperties;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;

uses(TestCase::class);

afterEach(function (): void {
    Telescope::stopRecording();
    Telescope::flushEntries();
});

it('records safe discord request metadata in telescope', function (): void {
    $entries = [];
    Telescope::afterRecording(function (Telescope $telescope, IncomingEntry $entry) use (&$entries): void {
        $entries[] = $entry;
    });
    Telescope::startRecording(false);

    $request = new DiscordRequest(
        HttpMethod::POST,
        '/webhooks/{webhook_id}/{webhook_token}',
        ['webhook_id' => '123', 'webhook_token' => 'secret-token'],
        body: ['content' => 'private message'],
        authentication: AuthenticationRequirement::NONE,
        resource: 'webhooks',
        endpoint: 'execute',
    );
    $psrResponse = new Response(204, [
        'X-RateLimit-Remaining' => '4',
        'X-RateLimit-Bucket' => 'bucket-id',
    ]);
    $response = new DiscordResponse($psrResponse, '');

    $event = new ResponseReceived($request, $response, 'request-id', 12.345, 2);
    event($event);

    expect($entries)->toHaveCount(1);

    $entry = $entries[0];
    expect($entry->content['uri'])->toBe('https://discord.com/api/v10/webhooks/{webhook_id}/{webhook_token}')
        ->and($entry->content['payload'])->toMatchArray([
            'request_id' => 'request-id',
            'resource' => 'webhooks',
            'endpoint' => 'execute',
            'attempts' => 2,
        ])
        ->and($entry->content['duration'])->toBe(12.35)
        ->and($entry->content['response_status'])->toBe(204)
        ->and($entry->content['response_headers']['x-ratelimit-bucket'])->toBe('bucket-id')
        ->and($entry->tags)->toContain('laracord', 'request:request-id', 'bucket:bucket-id')
        ->and(json_encode($entry->content))->not->toContain('secret-token', 'private message')
        ->and(json_encode(ExtractProperties::from($event)))->not->toContain('secret-token', 'private message');
});

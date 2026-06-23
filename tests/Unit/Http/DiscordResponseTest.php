<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Kyzegs\Laracord\Http\DiscordResponse;

it('handles json, empty bodies and rate headers', function (): void {
    $response = new DiscordResponse(new Response(200, [
        'X-RateLimit-Limit' => '5',
        'X-RateLimit-Remaining' => '4',
    ]), '{"user":{"id":"123"}}');

    expect($response->json('user.id'))->toBe('123')
        ->and($response->rateLimit()['remaining'])->toBe(4)
        ->and($response->isNoContent())->toBeFalse();

    $empty = new DiscordResponse(new Response(204), '');
    expect($empty->json())->toBeNull()->and($empty->isNoContent())->toBeTrue();
});

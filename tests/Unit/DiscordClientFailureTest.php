<?php

declare(strict_types=1);

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Sleep;
use Kyzegs\Laracord\ClientFactory;
use Kyzegs\Laracord\DiscordClient;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Enums\HttpMethod;
use Kyzegs\Laracord\Exceptions\DiscordRateLimitException;
use Kyzegs\Laracord\Exceptions\DiscordServerException;
use Kyzegs\Laracord\Exceptions\DiscordTransportException;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Tests\TestCase;
use Kyzegs\Laracord\ValueObjects\Authentication;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

uses(TestCase::class);

beforeEach(function (): void {
    Sleep::fake();
});

function clientOn(MockHandler $mock): DiscordClient
{
    return resolve(ClientFactory::class)->make(Authentication::bot('secret'), HandlerStack::create($mock));
}

function channelGet(): DiscordRequest
{
    return new DiscordRequest(HttpMethod::GET, '/channels/1');
}

function webhookPost(): DiscordRequest
{
    return new DiscordRequest(
        HttpMethod::POST,
        '/webhooks/{webhook_id}/{webhook_token}',
        ['webhook_id' => '1', 'webhook_token' => 'secret-token'],
        authentication: AuthenticationRequirement::NONE,
    );
}

function connectionFailure(string $method = 'GET'): ConnectException
{
    return new ConnectException('cURL error 28: Operation timed out', new Request($method, 'https://discord.com/api/v10/channels/1'));
}

/** @return list<string> every message in the exception chain */
function messageChain(Throwable $exception): array
{
    $messages = [];
    for ($current = $exception; $current instanceof Throwable; $current = $current->getPrevious()) {
        $messages[] = $current->getMessage();
    }

    return $messages;
}

function caught(callable $callback): Throwable
{
    try {
        $callback();
    } catch (Throwable $throwable) {
        return $throwable;
    }

    throw new RuntimeException('Expected an exception.');
}

it('retries reads after a server error', function (int $status): void {
    $mock = new MockHandler([new Response($status), new Response(200, [], '{"ok":true}')]);

    expect(clientOn($mock)->send(channelGet())->json('ok'))->toBeTrue()
        ->and($mock->count())->toBe(0);
})->with([500, 502, 503, 504, 524]);

it('retries reads after a connection failure', function (): void {
    $mock = new MockHandler([connectionFailure(), new Response(200, [], '{"ok":true}')]);

    expect(clientOn($mock)->send(channelGet())->json('ok'))->toBeTrue()
        ->and($mock->count())->toBe(0);
});

it('gives up on reads after the configured retries', function (): void {
    config()->set('laracord.http.server_retries', 2);
    $mock = new MockHandler([new Response(500), new Response(500), new Response(200)]);

    expect(fn (): DiscordResponse => clientOn($mock)->send(channelGet()))->toThrow(DiscordServerException::class)
        ->and($mock->count())->toBe(1);
});

it('does not repeat a POST after a server error', function (): void {
    $mock = new MockHandler([new Response(500), new Response(200)]);

    expect(fn (): DiscordResponse => clientOn($mock)->send(webhookPost()))->toThrow(DiscordServerException::class)
        ->and($mock->count())->toBe(1);
});

it('does not repeat a POST after a timeout', function (): void {
    $mock = new MockHandler([connectionFailure('POST'), new Response(200)]);

    expect(fn (): DiscordResponse => clientOn($mock)->send(webhookPost()))->toThrow(DiscordTransportException::class)
        ->and($mock->count())->toBe(1);
});

it('reports every 5xx as a server error', function (int $status): void {
    config()->set('laracord.http.server_retries', 1);
    $mock = new MockHandler([new Response($status, [], '{"message":"Unavailable"}')]);

    expect(fn (): DiscordResponse => clientOn($mock)->send(channelGet()))->toThrow(DiscordServerException::class);
})->with([500, 503, 599]);

it('keeps tokens out of transport errors', function (string $exceptionClass): void {
    $url = 'https://discord.com/api/v10/webhooks/1/secret-token';
    $mock = new MockHandler([new $exceptionClass('cURL error 35: failed for '.$url, new Request('POST', $url))]);

    $exception = caught(fn (): DiscordResponse => clientOn($mock)->send(webhookPost()));

    expect($exception)->toBeInstanceOf(DiscordTransportException::class)
        ->and(implode(' ', messageChain($exception)))->not->toContain('secret-token')
        ->and($exception->getMessage())->toContain('/webhooks/1/[REDACTED]');
})->with([ConnectException::class, RequestException::class]);

it('keeps tokens out of rate limit errors', function (): void {
    config()->set('laracord.rate_limit.max_retries', 0);
    $mock = new MockHandler([new Response(429, ['Retry-After' => '1'], '{"retry_after":1,"global":false}')]);

    $exception = caught(fn (): DiscordResponse => clientOn($mock)->send(webhookPost()));

    expect($exception)->toBeInstanceOf(DiscordRateLimitException::class)
        ->and(implode(' ', messageChain($exception)))->not->toContain('secret-token');
});

it('keeps tokens out of rate limit logs', function (): void {
    config()->set('laracord.rate_limit.safety_buffer_seconds', 0.0);
    config()->set('laracord.rate_limit.jitter_percent', 0.0);

    $logger = new class extends AbstractLogger
    {
        /** @var list<string> */
        public array $messages = [];

        public function log($level, Stringable|string $message, array $context = []): void
        {
            $this->messages[] = $message.' '.json_encode($context);
        }
    };
    app()->instance(LoggerInterface::class, $logger);

    $mock = new MockHandler([
        new Response(429, ['Retry-After' => '0'], '{"retry_after":0,"global":false}'),
        new Response(200, [], '{"ok":true}'),
    ]);

    expect(clientOn($mock)->send(webhookPost())->json('ok'))->toBeTrue()
        ->and($logger->messages)->not->toBeEmpty()
        ->and(implode(' ', $logger->messages))->not->toContain('secret-token')
        ->and(implode(' ', $logger->messages))->toContain('{webhook_token}');
});

it('reports a rate limit lock timeout as a Discord exception', function (): void {
    $mock = new MockHandler([new LockTimeoutException]);

    expect(fn (): DiscordResponse => clientOn($mock)->send(channelGet()))->toThrow(DiscordTransportException::class);
});

it('does not treat an empty max delay as zero', function (): void {
    config()->set('laracord.rate_limit.max_delay_seconds', '');
    // Above zero, or the middleware's `$delay > $max` check could not fail with a max of 0.0.
    config()->set('laracord.rate_limit.safety_buffer_seconds', 0.001);

    $mock = new MockHandler([
        new Response(429, ['Retry-After' => '0'], '{"retry_after":0,"global":false}'),
        new Response(200, [], '{"ok":true}'),
    ]);

    expect(clientOn($mock)->send(channelGet())->json('ok'))->toBeTrue();
});

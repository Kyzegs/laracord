<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Observability;

use Illuminate\Contracts\Config\Repository;
use Kyzegs\Laracord\Events\RequestFailed;
use Kyzegs\Laracord\Events\ResponseReceived;
use Kyzegs\Laracord\Exceptions\DiscordHttpException;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Throwable;

final readonly class TelescopeRecorder
{
    public function __construct(private Repository $config) {}

    public function recordResponse(ResponseReceived $event): void
    {
        $this->record($event->request, $event->response, null, $event->requestId, $event->durationMs, $event->attempts);
    }

    public function recordFailure(RequestFailed $event): void
    {
        $response = $event->exception instanceof DiscordHttpException ? $event->exception->response : null;

        $this->record($event->request, $response, $event->exception, $event->requestId, $event->durationMs, $event->attempts);
    }

    private function record(
        DiscordRequest $request,
        ?DiscordResponse $response,
        ?Throwable $exception,
        string $requestId,
        float $durationMs,
        int $attempts,
    ): void {
        if (! Telescope::isRecording()) {
            return;
        }

        $rateLimit = $response?->rateLimit();
        $status = $response?->status();
        $content = [
            'method' => $request->method->value,
            'uri' => $this->uri($request),
            'headers' => [],
            'payload' => array_filter([
                'request_id' => $requestId ?: null,
                'resource' => $request->resource,
                'endpoint' => $request->endpoint,
                'attempts' => $attempts,
                'exception' => $exception ? $exception::class : null,
            ], fn (mixed $value): bool => $value !== null),
            'duration' => round($durationMs, 2),
        ];

        if ($response !== null) {
            $rateLimit = $response->rateLimit();
            $content += [
                'response_status' => $status,
                'response_headers' => array_filter([
                    'x-ratelimit-limit' => $rateLimit['limit'],
                    'x-ratelimit-remaining' => $rateLimit['remaining'],
                    'x-ratelimit-reset-after' => $rateLimit['reset_after'],
                    'x-ratelimit-bucket' => $rateLimit['bucket'],
                    'x-ratelimit-scope' => $rateLimit['scope'],
                ], fn (mixed $value): bool => $value !== null),
                'response' => 'Body not recorded by Laracord.',
            ];
        }

        Telescope::recordClientRequest(
            IncomingEntry::make($content)->tags(array_filter([
                'laracord',
                $request->resource ? 'resource:'.$request->resource : null,
                $request->endpoint ? 'endpoint:'.$request->endpoint : null,
                $status ? 'status:'.$status : null,
                isset($rateLimit['bucket']) ? 'bucket:'.$rateLimit['bucket'] : null,
                $requestId ? 'request:'.$requestId : null,
            ])),
        );
    }

    private function uri(DiscordRequest $request): string
    {
        $base = rtrim((string) $this->config->get('laracord.api_url', 'https://discord.com/api'), '/');
        $version = (int) $this->config->get('laracord.api_version', 10);

        return $base.'/v'.$version.$request->path;
    }
}

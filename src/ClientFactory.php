<?php

declare(strict_types=1);

namespace Kyzegs\Laracord;

use Composer\InstalledVersions;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Kyzegs\GuzzleRateLimitMiddleware\Config\GlobalLimit;
use Kyzegs\GuzzleRateLimitMiddleware\Config\InvalidRequestLimit;
use Kyzegs\GuzzleRateLimitMiddleware\Config\Options;
use Kyzegs\GuzzleRateLimitMiddleware\RateLimitMiddleware;
use Kyzegs\Laracord\RateLimit\LaravelLockFactory;
use Kyzegs\Laracord\RateLimit\LaravelStore;
use Kyzegs\Laracord\RateLimit\RedactingLogger;
use Kyzegs\Laracord\ValueObjects\Authentication;
use Psr\Log\LoggerInterface;

final readonly class ClientFactory
{
    public function __construct(
        private ConfigRepository $configRepository,
        private CacheRepository $cacheRepository,
        private LoggerInterface $logger,
        private Dispatcher $events,
    ) {}

    public function make(Authentication $authentication, ?HandlerStack $handlerStack = null): DiscordClient
    {
        $rate = (array) $this->configRepository->get('laracord.rate_limit', []);
        $stack = $handlerStack ?? HandlerStack::create();
        $options = new Options(
            maxRetries: (int) ($rate['max_retries'] ?? 5),
            safetyBufferSeconds: (float) ($rate['safety_buffer_seconds'] ?? 0.25),
            jitterPercent: (float) ($rate['jitter_percent'] ?? 2.0),
            globalLimit: new GlobalLimit((int) ($rate['global_requests'] ?? 50), (float) ($rate['global_window_seconds'] ?? 1.0)),
            invalidRequestLimit: new InvalidRequestLimit((int) ($rate['invalid_requests'] ?? 9000), (float) ($rate['invalid_window_seconds'] ?? 600.0)),
            maxDelaySeconds: is_numeric($rate['max_delay_seconds'] ?? null) ? (float) $rate['max_delay_seconds'] : null,
        );
        $laravelStore = new LaravelStore($this->cacheRepository, (string) ($rate['cache_prefix'] ?? 'laracord:rate-limit:'));
        $laravelLockFactory = new LaravelLockFactory(
            $this->cacheRepository,
            (string) ($rate['lock_prefix'] ?? 'laracord:lock:'),
            (int) ($rate['lock_ttl_seconds'] ?? 60),
            (int) ($rate['lock_wait_seconds'] ?? 10),
        );
        $stack->push(RateLimitMiddleware::discord($laravelStore, new RedactingLogger($this->logger), $laravelLockFactory, $options), 'discord_rate_limits');

        $client = new GuzzleClient([
            'handler' => $stack,
            'base_uri' => self::baseUrl($this->configRepository),
            'http_errors' => false,
            'timeout' => (float) $this->configRepository->get('laracord.http.timeout', 30.0),
            'connect_timeout' => (float) $this->configRepository->get('laracord.http.connect_timeout', 10.0),
            'headers' => ['User-Agent' => (string) ($this->configRepository->get('laracord.user_agent') ?: 'DiscordBot (https://github.com/Kyzegs/laracord, '.InstalledVersions::getPrettyVersion('kyzegs/laracord').')')],
        ]);

        return new DiscordClient($this, $client, $authentication, $this->configRepository, $this->events);
    }

    public static function baseUrl(ConfigRepository $configRepository): string
    {
        return rtrim((string) $configRepository->get('laracord.api_url', 'https://discord.com/api'), '/').'/v'.(int) $configRepository->get('laracord.api_version', 10).'/';
    }
}

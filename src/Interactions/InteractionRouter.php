<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Interactions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Kyzegs\Laracord\Interactions\Enums\InteractionType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Maps incoming Discord interactions to handlers and dispatches them.
 *
 * Handlers may be closures, invokable class names, `Class@method` strings, or
 * `[Class::class, 'method']` pairs. They receive the resolved Interaction (and,
 * for wildcard custom_id routes, the captured `$parameters`) and must return a
 * Symfony Response — use InteractionResponse to build one.
 */
final class InteractionRouter
{
    /** @var array<string, callable|string|array{0: class-string|object, 1: string}> */
    private array $commands = [];

    /** @var array<string, callable|string|array{0: class-string|object, 1: string}> */
    private array $autocompletes = [];

    /** @var array<string, callable|string|array{0: class-string|object, 1: string}> */
    private array $components = [];

    /** @var array<string, callable|string|array{0: class-string|object, 1: string}> */
    private array $modals = [];

    public function __construct(private readonly Container $container) {}

    /** @param callable|string|array{0: class-string|object, 1: string} $handler */
    public function command(string $name, callable|string|array $handler): self
    {
        $this->commands[$name] = $handler;

        return $this;
    }

    /** @param callable|string|array{0: class-string|object, 1: string} $handler */
    public function autocomplete(string $name, callable|string|array $handler): self
    {
        $this->autocompletes[$name] = $handler;

        return $this;
    }

    /** @param callable|string|array{0: class-string|object, 1: string} $handler */
    public function component(string $customId, callable|string|array $handler): self
    {
        $this->components[$customId] = $handler;

        return $this;
    }

    /** @param callable|string|array{0: class-string|object, 1: string} $handler */
    public function modal(string $customId, callable|string|array $handler): self
    {
        $this->modals[$customId] = $handler;

        return $this;
    }

    public function handle(Request|Interaction $interaction): Response
    {
        $interaction = $interaction instanceof Interaction ? $interaction : Interaction::from($interaction);

        return match ($interaction->type()) {
            InteractionType::PING->value => InteractionResponse::pong(),
            InteractionType::APPLICATION_COMMAND->value => $this->dispatchNamed($this->commands, $interaction->commandName(), $interaction),
            InteractionType::APPLICATION_COMMAND_AUTOCOMPLETE->value => $this->dispatchNamed($this->autocompletes, $interaction->commandName(), $interaction),
            InteractionType::MESSAGE_COMPONENT->value => $this->dispatchPattern($this->components, $interaction->customId(), $interaction),
            InteractionType::MODAL_SUBMIT->value => $this->dispatchPattern($this->modals, $interaction->customId(), $interaction),
            default => throw new UnhandledInteractionException('Unsupported interaction type '.$interaction->type().'.'),
        };
    }

    /** @param array<string, callable|string|array{0: class-string|object, 1: string}> $handlers */
    private function dispatchNamed(array $handlers, ?string $name, Interaction $interaction): Response
    {
        if ($name === null || ! isset($handlers[$name])) {
            throw new UnhandledInteractionException('No handler registered for interaction "'.($name ?? '').'".');
        }

        return $this->invoke($handlers[$name], $interaction, []);
    }

    /** @param array<string, callable|string|array{0: class-string|object, 1: string}> $handlers */
    private function dispatchPattern(array $handlers, ?string $customId, Interaction $interaction): Response
    {
        if ($customId !== null) {
            foreach ($handlers as $pattern => $handler) {
                $captures = $this->match($pattern, $customId);
                if ($captures !== null) {
                    return $this->invoke($handler, $interaction, $captures);
                }
            }
        }

        throw new UnhandledInteractionException('No handler registered for custom_id "'.($customId ?? '').'".');
    }

    /** @return list<string>|null captured wildcard segments, or null when the pattern does not match */
    private function match(string $pattern, string $value): ?array
    {
        if (! str_contains($pattern, '*')) {
            return $pattern === $value ? [] : null;
        }

        $regex = '#^'.str_replace('\*', '(.*)', preg_quote($pattern, '#')).'$#';

        return preg_match($regex, $value, $matches) === 1 ? array_values(array_slice($matches, 1)) : null;
    }

    /**
     * @param  callable|string|array{0: class-string|object, 1: string}  $handler
     * @param  list<string>  $captures
     */
    private function invoke(callable|string|array $handler, Interaction $interaction, array $captures): Response
    {
        if (is_string($handler) && ! str_contains($handler, '@') && class_exists($handler)) {
            $instance = $this->container->make($handler);
            $callable = [$instance, '__invoke'];
        } else {
            $callable = $handler;
        }

        /** @var callable|string $callable */
        $response = $this->container->call($callable, ['interaction' => $interaction, 'parameters' => $captures]);

        if (! $response instanceof Response) {
            throw new UnhandledInteractionException('Interaction handler must return a '.Response::class.'.');
        }

        return $response;
    }
}

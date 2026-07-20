<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Interactions;

use Illuminate\Http\JsonResponse;
use Kyzegs\Laracord\Contracts\Client;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Payloads\DiscordMessage;

final readonly class InteractionContext
{
    public function __construct(public Interaction $interaction, private Client $client) {}

    public function defer(bool $ephemeral = false): JsonResponse
    {
        return InteractionResponse::defer($ephemeral);
    }

    public function deferUpdate(): JsonResponse
    {
        return InteractionResponse::deferUpdate();
    }

    public function getOriginal(): DiscordResponse
    {
        return $this->call('getOriginal');
    }

    /** @param array<string, mixed>|DiscordMessage $message */
    public function editOriginal(DiscordMessage|array $message): DiscordResponse
    {
        return $this->callWithMessage('editOriginal', $message);
    }

    public function deleteOriginal(): DiscordResponse
    {
        return $this->call('deleteOriginal');
    }

    /** @param array<string, mixed>|DiscordMessage $message */
    public function followup(DiscordMessage|array $message): DiscordResponse
    {
        return $this->callWithMessage('createFollowup', $message, query: ['wait' => true]);
    }

    public function getFollowup(string $messageId): DiscordResponse
    {
        return $this->call('getFollowup', ['message_id' => $messageId]);
    }

    /** @param array<string, mixed>|DiscordMessage $message */
    public function editFollowup(string $messageId, DiscordMessage|array $message): DiscordResponse
    {
        return $this->callWithMessage('editFollowup', $message, ['message_id' => $messageId]);
    }

    public function deleteFollowup(string $messageId): DiscordResponse
    {
        return $this->call('deleteFollowup', ['message_id' => $messageId]);
    }

    /**
     * @param  array<string, string>  $parameters
     * @param  array<string, mixed>  $query
     */
    private function call(string $endpoint, array $parameters = [], array $query = []): DiscordResponse
    {
        return $this->client->resource('interactions')->call(
            $endpoint,
            [...$this->parameters(), ...$parameters],
            query: $query,
        );
    }

    /**
     * @param  array<string, mixed>|DiscordMessage  $message
     * @param  array<string, string>  $parameters
     * @param  array<string, mixed>  $query
     */
    private function callWithMessage(string $endpoint, DiscordMessage|array $message, array $parameters = [], array $query = []): DiscordResponse
    {
        return $this->client->resource('interactions')->call(
            $endpoint,
            [...$this->parameters(), ...$parameters],
            $message,
            $query,
            $message instanceof DiscordMessage ? $message->files() : [],
        );
    }

    /** @return array{application_id: string, interaction_token: string} */
    private function parameters(): array
    {
        if ($this->interaction->applicationId() === '' || $this->interaction->token() === '') {
            throw new \LogicException('Interaction lifecycle requests require application_id and token.');
        }

        if ($this->interaction->isExpired()) {
            throw new \LogicException('Interaction token expired after 15 minutes.');
        }

        return [
            'application_id' => $this->interaction->applicationId(),
            'interaction_token' => $this->interaction->token(),
        ];
    }
}

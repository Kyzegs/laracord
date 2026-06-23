<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Interactions;

use Illuminate\Http\JsonResponse;
use Kyzegs\Laracord\Payloads\DiscordMessage;
use Symfony\Component\HttpFoundation\Response;

final class InteractionResponse
{
    public static function pong(): JsonResponse
    {
        return new JsonResponse(['type' => 1]);
    }

    /** @param array<string, mixed>|DiscordMessage $message */
    public static function message(DiscordMessage|array $message): JsonResponse
    {
        return new JsonResponse(['type' => 4, 'data' => $message instanceof DiscordMessage ? $message->toArray() : $message]);
    }

    public static function defer(bool $ephemeral = false): JsonResponse
    {
        return new JsonResponse(['type' => 5, 'data' => $ephemeral ? ['flags' => 64] : []]);
    }

    /** @param array<string, mixed>|DiscordMessage $message */
    public static function update(DiscordMessage|array $message): JsonResponse
    {
        return new JsonResponse(['type' => 7, 'data' => $message instanceof DiscordMessage ? $message->toArray() : $message]);
    }

    /** @param list<array<string, mixed>> $choices */
    public static function autocomplete(array $choices): JsonResponse
    {
        return new JsonResponse(['type' => 8, 'data' => ['choices' => $choices]]);
    }

    /** @param list<array<string, mixed>> $components */
    public static function modal(string $customId, string $title, array $components): JsonResponse
    {
        return new JsonResponse(['type' => 9, 'data' => ['custom_id' => $customId, 'title' => $title, 'components' => $components]]);
    }

    public static function webhookPong(): Response
    {
        return new Response('', 204, ['Content-Type' => 'application/json']);
    }
}

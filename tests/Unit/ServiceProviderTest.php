<?php

declare(strict_types=1);

use Kyzegs\Laracord\Contracts\Client;
use Kyzegs\Laracord\Contracts\Factory;
use Kyzegs\Laracord\DiscordClient;
use Kyzegs\Laracord\Facades\Laracord;
use Kyzegs\Laracord\LaracordManager;
use Kyzegs\Laracord\Testing\FakeDiscordClient;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

it('registers manager and alias as singleton', function (): void {
    $laracordManager = resolve(LaracordManager::class);
    $alias = resolve('laracord');
    if (! $alias instanceof LaracordManager) {
        throw new RuntimeException('Laracord alias did not resolve the manager.');
    }

    expect($laracordManager)->toBe($alias);
    expect($alias)->toBe(resolve('laracord'));
});

it('makes the contracts injectable', function (): void {
    expect(resolve(Factory::class))->toBeInstanceOf(LaracordManager::class)
        ->and(resolve(Client::class))->toBeInstanceOf(DiscordClient::class);
});

it('resolves the contracts to the fake once faked', function (): void {
    $fake = Laracord::fake();

    expect(resolve(Factory::class))->toBe($fake)
        ->and(resolve(Client::class))->toBeInstanceOf(FakeDiscordClient::class);
});

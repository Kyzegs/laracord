<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\PendingCommand;
use Kyzegs\Laracord\Facades\Laracord;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    config()->set('laracord.application_id', 'app-1');
});

it('syncs global commands from config', function (): void {
    config()->set('laracord.commands', [['name' => 'ping', 'description' => 'Pong']]);
    $fake = Laracord::fake();

    expect(Artisan::call('laracord:commands:sync'))->toBe(0);

    $fake->assertSent('commands', 'bulkOverwriteGlobalCommands', fn (DiscordRequest $request): bool => $request->parameters['application_id'] === 'app-1'
        && json_encode($request->bodyArray()) === json_encode([['name' => 'ping', 'description' => 'Pong']]));
});

it('syncs to a guild when --guild is given', function (): void {
    config()->set('laracord.commands', [['name' => 'ping', 'description' => 'Pong']]);
    $fake = Laracord::fake();

    expect(Artisan::call('laracord:commands:sync', ['--guild' => '42']))->toBe(0);

    $fake->assertSent('commands', 'bulkOverwriteGuildCommands', fn (DiscordRequest $request): bool => $request->parameters['guild_id'] === '42');
});

it('refuses to wipe commands when the config is empty', function (array $options): void {
    config()->set('laracord.commands', []);
    $fake = Laracord::fake();

    expect(Artisan::call('laracord:commands:sync', $options))->toBe(1);

    $fake->assertNothingSent();
})->with([
    'global' => [[]],
    'guild' => [['--guild' => '42']],
]);

it('wipes commands from an empty config only with --force', function (): void {
    config()->set('laracord.commands', []);
    $fake = Laracord::fake();

    expect(Artisan::call('laracord:commands:sync', ['--force' => true]))->toBe(0);

    $fake->assertSent('commands', 'bulkOverwriteGlobalCommands');
});

it('lists registered commands', function (): void {
    $fake = Laracord::fake([
        'commands.listGlobalCommands' => Laracord::response([
            ['id' => '1', 'name' => 'ping', 'description' => 'Pong'],
        ]),
    ]);

    expect(Artisan::call('laracord:commands:list'))->toBe(0);

    $fake->assertSent('commands', 'listGlobalCommands');
});

it('clears all global commands with --force', function (): void {
    $fake = Laracord::fake();

    expect(Artisan::call('laracord:commands:clear', ['--force' => true]))->toBe(0);

    $fake->assertSent('commands', 'bulkOverwriteGlobalCommands', fn (DiscordRequest $request): bool => $request->bodyArray() === []);
});

it('asks before clearing commands', function (): void {
    /** @var TestCase $this */
    $fake = Laracord::fake();

    $command = $this->artisan('laracord:commands:clear');
    assert($command instanceof PendingCommand);
    $command->expectsConfirmation('Remove all global Discord commands?', 'no')->assertExitCode(1)->run();

    $fake->assertNothingSent();
});

it('fails without an application id', function (): void {
    config()->set('laracord.application_id');
    Laracord::fake();

    expect(Artisan::call('laracord:commands:sync'))->toBe(1);
});

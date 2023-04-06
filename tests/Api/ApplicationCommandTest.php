<?php

use Illuminate\Support\Facades\Cache;
use Kyzegs\Laracord\Facades\ApplicationCommand;

dataset('guildId', [env('DISCORD_GUILD_ID')]);

dataset('applicationCommands', [[[
    [
        'name' => 'suggestion',
        'description' => 'Create a suggestion',
    ],
    [
        'name' => 'form',
        'description' => 'Open a form',
    ],
]]]);

dataset('applicationCommand', [[
    [
        'name' => 'ban',
        'description' => 'Ban a user',
    ],
]]);

it('bulk overwrite global application commands', function (array $data) {
    expect(ApplicationCommand::bulk($data))->toBeCollectionOfApplicationCommands(2);
})->with('applicationCommands');

it('bulk overwrite guild application commands', function (string $guildId, array $data) {
    expect(ApplicationCommand::bulk($data, $guildId))->toBeCollectionOfApplicationCommands(2, true);
})->with('guildId', 'applicationCommands');

it('rate limit handling', function (string $guildId, array $data) {
    expect(ApplicationCommand::bulk($data, $guildId))->toBeTruthy();
})->with('guildId', 'applicationCommands');

it('create global application command', function (array $data) {
    expect(ApplicationCommand::create($data))->toBeApplicationCommand();
    expect(ApplicationCommand::newInstance(['name' => 'sticker', 'description' => 'Post a sticker'])->save())->toBeApplicationCommand();
})->with('applicationCommand');

it('create guild application command', function (string $guildId, array $data) {
    expect(ApplicationCommand::create($data, $guildId))->toBeApplicationCommand(true);
    expect(ApplicationCommand::newInstance(['guild_id' => $guildId, 'name' => 'sticker', 'description' => 'Post a sticker'])->save())->toBeApplicationCommand(true);
})->with('guildId', 'applicationCommand');

it('get global application commands', function () {
    expect(ApplicationCommand::get())->toBeCollectionOfApplicationCommands(4, false, false);
});

it('get guild application commands', function (string $guildId) {
    expect(ApplicationCommand::get($guildId))->toBeCollectionOfApplicationCommands(4, true, false);
})->with('guildId');

it('global application command cache', function () {
    expect(Cache::get('application-commands:0'))->toBeCollectionOfApplicationCommands(4, false, false);
});

it('guild application command cache', function (string $guildId) {
    expect(Cache::get(sprintf('application-commands:%s', $guildId)))->toBeCollectionOfApplicationCommands(4, true, false);
})->with('guildId');

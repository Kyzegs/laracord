<?php

use Illuminate\Support\Collection;
use Kyzegs\Laracord\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)->in('Api');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeApplicationCommand', function (bool $guildScope = false, bool $hasLocalizations = true) {
    return $this
        ->toBeInstanceOf(\Kyzegs\Laracord\Models\ApplicationCommand::class)
        ->toHaveKeys(
            collect([
                'id',
                'type',
                'application_id',
                'name',
                'description',
                'default_member_permissions',
                'default_permission',
                'version',
            ])
            ->when($guildScope, fn (Collection $keys) => $keys->push('guild_id'))
            ->when($hasLocalizations, fn (Collection $keys) => $keys->merge(['name_localizations', 'description_localizations']))
            ->toArray()
        );
});

expect()->extend('toBeCollectionOfApplicationCommands', function (int $count, bool $guildScope = false, bool $hasLocalizations = true) {
    return $this->toBeCollection()->toHaveCount($count)->each->toBeApplicationCommand($guildScope, $hasLocalizations);
});

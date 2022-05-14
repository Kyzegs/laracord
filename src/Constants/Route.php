<?php

namespace Kyzegs\Laracord\Constants;

enum Route: string
{
    case GLOBAL_APPLICATION_COMMANDS = '/applications/%d/commands';
    case GLOBAL_APPLICATION_COMMAND = '/applications/%d/commands/%d';

    case GUILD_APPLICATION_COMMANDS = '/applications/%d/guilds/%d/commands';
    case GUILD_APPLICATION_COMMAND = '/applications/%d/guilds/%d/commands/%d';
    case GUILD_APPLICATION_COMMAND_PERMISSONS = '/applications/%d/guilds/%d/commands/permissions';

    case APPLICATION_COMMAND_PERMISSIONS = '/applications/%d/guilds/%d/commands/%d/permissions';
}

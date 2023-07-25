<?php

namespace Kyzegs\Laracord\Constants;

class Routes
{
    const BASE_URL = 'https://discord.com/api/v9/';

    const GET_GLOBAL_APPLICATION_COMMANDS = '/applications/%d/commands';

    const CREATE_GLOBAL_APPLICATION_COMMAND = '/applications/%d/commands';

    const GET_GLOBAL_APPLICATION_COMMAND = '/applications/%d/commands/%d';

    const EDIT_GLOBAL_APPLICATION_COMMAND = '/applications/%d/commands/%d';

    const DELETE_GLOBAL_APPLICATION_COMMAND = '/applications/%d/commands/%d';

    const BULK_OVERWRITE_GLOBAL_APPLICATION_COMMANDS = '/applications/%d/commands';

    const GET_GUILD_APPLICATION_COMMANDS = '/applications/%d/guilds/%d/commands';

    const CREATE_GUILD_APPLICATION_COMMAND = '/applications/%d/guilds/%d/commands';

    const GET_GUILD_APPLICATION_COMMAND = '/applications/%d/guilds/%d/commands/%d';

    const EDIT_GUILD_APPLICATION_COMMAND = '/applications/%d/guilds/%d/commands/%d';

    const DELETE_GUILD_APPLICATION_COMMAND = '/applications/%d/guilds/%d/commands/%d';

    const BULK_OVERWRITE_GUILD_APPLICATION_COMMANDS = '/applications/%d/guilds/%d/commands';
}

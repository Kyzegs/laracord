<?php

namespace Kyzegs\Laracord\Constants;

class Routes
{
    const BASE_URL = 'https://discord.com/api/v8/';

    CONST GET_GLOBAL_APPLICATION_COMMANDS = '/applications/%d/commands';
    CONST CREATE_GLOBAL_APPLICATION_COMMAND = '/applications/%d/commands';
    CONST GET_GLOBAL_APPLICATION_COMMAND = '/applications/%d/commands/%d';
    CONST EDIT_GLOBAL_APPLICATION_COMMAND = '/applications/%d/commands/%d';
    CONST DELETE_GLOBAL_APPLICATION_COMMAND = '/applications/%d/commands/%d';
    CONST BULK_OVERWRITE_GLOBAL_APPLICATION_COMMANDS = '/applications/%d/commands';

    CONST GET_GUILD_APPLICATION_COMMANDS = '/applications/%d/guilds/%d/commands';
    CONST CREATE_GUILD_APPLICATION_COMMAND = '/applications/%d/guilds/%d/commands';
    CONST GET_GUILD_APPLICATION_COMMAND = '/applications/%d/guilds/%d/commands/%d';
    CONST EDIT_GUILD_APPLICATION_COMMAND = '/applications/%d/guilds/%d/commands/%d';
    CONST DELETE_GUILD_APPLICATION_COMMAND = '/applications/%d/guilds/%d/commands/%d';
    CONST BULK_OVERWRITE_GUILD_APPLICATION_COMMANDS = '/applications/%d/guilds/%d/commands';

    CONST GET_GUILD_APPLICATION_COMMAND_PERMISSIONS = '/applications/%d/guilds/%d/commands/permissions';
    CONST GET_APPLICATION_COMMAND_PERMISSIONS = '/applications/%d/guilds/%d/commands/%d/permissions';
    CONST EDIT_APPLICATION_COMMAND_PERMISSIONS ='/applications/%d/guilds/%d/commands/%d/permissions';
    CONST BATCH_EDIT_APPLICATION_COMMAND_PERMISSIONS ='/applications/%d/guilds/%d/commands/permissions';
}

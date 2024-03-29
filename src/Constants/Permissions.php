<?php

namespace Kyzegs\Laracord\Constants;

class Permissions
{
    const CREATE_INSTANT_INVITE = 0x1;
    const KICK_MEMBERS = 0x2;
    const BAN_MEMBERS = 0x4;
    const ADMINISTRATOR = 0x8;
    const MANAGE_CHANNELS = 0x10;
    const MANAGE_GUILD = 0x20;
    const ADD_REACTIONS = 0x40;
    const VIEW_AUDIT_LOG = 0x80;
    const PRIORITY_SPEAKER = 0x100;
    const STREAM = 0x200;
    const VIEW_CHANNEL = 0x400;
    const SEND_MESSAGES = 0x800;
    const SEND_TTS_MESSAGES = 0x1000;
    const MANAGE_MESSAGES = 0x2000;
    const EMBED_LINKS = 0x4000;
    const ATTACH_FILES = 0x8000;
    const READ_MESSAGE_HISTORY = 0x10000;
    const MENTION_EVERYONE = 0x20000;
    const USE_EXTERNAL_EMOJIS = 0x40000;
    const VIEW_GUILD_INSIGHTS = 0x80000;
    const CONNECT = 0x100000;
    const SPEAK = 0x200000;
    const MUTE_MEMBERS = 0x400000;
    const DEAFEN_MEMBERS = 0x800000;
    const MOVE_MEMBERS = 0x1000000;
    const USE_VAD = 0x2000000;
    const CHANGE_NICKNAME = 0x4000000;
    const MANAGE_NICKNAMES = 0x8000000;
    const MANAGE_ROLES = 0x10000000;
    const MANAGE_WEBHOOKS = 0x20000000;
    const MANAGE_EMOJIS_AND_STICKERS = 0x40000000;
    const USE_APPLICATION_COMMANDS = 0x80000000;
    const REQUEST_TO_SPEAK = 0x100000000;
    const MANAGE_THREADS = 0x400000000;
    const CREATE_PUBLIC_THREADS = 0x800000000;
    const CREATE_PRIVATE_THREADS = 0x1000000000;
    const USE_EXTERNAL_STICKERS = 0x2000000000;
    const SEND_MESSAGES_IN_THREADS = 0x4000000000;
    const START_EMBEDDED_ACTIVITIES = 0x8000000000;
}

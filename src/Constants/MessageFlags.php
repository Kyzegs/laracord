<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Constants;

class MessageFlags
{
    const CROSSPOSTED = 1 << 0;

    const IS_CROSSPOST = 1 << 1;

    const SUPPRESS_EMBEDS = 1 << 2;

    const SOURCE_MESSAGE_DELETED = 1 << 3;

    const URGENT = 1 << 4;

    const HAS_THREAD = 1 << 5;

    const EPHEMERAL = 1 << 6;

    const LOADING = 1 << 7;

    const FAILED_TO_MENTION_SOME_ROLES_IN_THREAD = 1 << 8;

    const SUPPRESS_NOTIFICATIONS = 1 << 12;

    const IS_VOICE_MESSAGE = 1 << 13;

    const HAS_SNAPSHOT = 1 << 14;

    const IS_COMPONENTS_V2 = 1 << 15;
}

<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Enums;

enum AuthenticationType: string
{
    case BOT = 'Bot';
    case BEARER = 'Bearer';
    case NONE = '';
}

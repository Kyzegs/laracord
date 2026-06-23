<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Enums;

enum AuthenticationType: string
{
    case Bot = 'Bot';
    case Bearer = 'Bearer';
    case None = '';
}

<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Enums;

enum AuthenticationRequirement
{
    case REQUIRED;
    case OPTIONAL;
    case NONE;
}

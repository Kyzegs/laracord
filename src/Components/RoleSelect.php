<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;

final class RoleSelect extends SelectMenu
{
    protected function type(): ComponentType
    {
        return ComponentType::ROLE_SELECT;
    }
}

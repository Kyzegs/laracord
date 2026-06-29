<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;

final class MentionableSelect extends SelectMenu
{
    protected function type(): ComponentType
    {
        return ComponentType::MENTIONABLE_SELECT;
    }
}

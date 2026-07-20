<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;

final class MentionableSelect extends SelectMenu
{
    public function defaultUser(string $id): self
    {
        return $this->addDefaultValue($id, 'user');
    }

    public function defaultRole(string $id): self
    {
        return $this->addDefaultValue($id, 'role');
    }

    protected function type(): ComponentType
    {
        return ComponentType::MENTIONABLE_SELECT;
    }
}

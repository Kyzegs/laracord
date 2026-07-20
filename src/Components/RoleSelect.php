<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;

final class RoleSelect extends SelectMenu
{
    public function defaultRole(string $id): self
    {
        return $this->addDefaultValue($id, 'role');
    }

    protected function type(): ComponentType
    {
        return ComponentType::ROLE_SELECT;
    }
}

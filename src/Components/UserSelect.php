<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;

final class UserSelect extends SelectMenu
{
    public function defaultUser(string $id): self
    {
        return $this->addDefaultValue($id, 'user');
    }

    protected function type(): ComponentType
    {
        return ComponentType::USER_SELECT;
    }
}

<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;

final class ChannelSelect extends SelectMenu
{
    /** @var list<int>|null */
    private ?array $channelTypes = null;

    public function defaultChannel(string $id): self
    {
        return $this->addDefaultValue($id, 'channel');
    }

    public function channelTypes(int ...$channelTypes): self
    {
        $this->channelTypes = array_values($channelTypes);

        return $this;
    }

    protected function type(): ComponentType
    {
        return ComponentType::CHANNEL_SELECT;
    }

    /** @return array<string, mixed> */
    #[\Override]
    protected function extra(): array
    {
        return $this->channelTypes === null ? [] : ['channel_types' => $this->channelTypes];
    }
}

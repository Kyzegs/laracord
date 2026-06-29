<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;

final class StringSelect extends SelectMenu
{
    /** @var list<SelectOption> */
    private array $options = [];

    public function option(SelectOption $option): self
    {
        if (count($this->options) >= 25) {
            throw new \InvalidArgumentException('A string select supports at most 25 options.');
        }

        $this->options[] = $option;

        return $this;
    }

    public function options(SelectOption ...$options): self
    {
        foreach ($options as $option) {
            $this->option($option);
        }

        return $this;
    }

    protected function type(): ComponentType
    {
        return ComponentType::STRING_SELECT;
    }

    /** @return array<string, mixed> */
    protected function extra(): array
    {
        if ($this->options === []) {
            throw new \InvalidArgumentException('A string select requires at least one option.');
        }

        return ['options' => array_map(static fn (SelectOption $option): array => $option->toArray(), $this->options)];
    }
}

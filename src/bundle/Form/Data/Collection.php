<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Data;

abstract class Collection
{
    protected $items = [];
    protected $updatedIds = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if (is_callable([$item, 'getId'])) {
                $this->items[$item->getId()] = $item;
            }
        }
    }

    protected function getItemsProperty(string $property): array
    {
        $getter = 'get' . ucfirst($property);

        $propertyValues = [];
        foreach ($this->items as $id => $item) {
            $propertyValues[$id] = $item->{$getter}();
        }

        return $propertyValues;
    }

    protected function setItemsProperty(array $data, string $property): void
    {
        $property = ucfirst($property);
        $getter = 'get' . $property;
        $setter = 'set' . $property;

        foreach ($data as $id => $value) {
            if (!isset($this->items[$id])) {
                continue;
            }

            $currentValue = $this->items[$id]->{$getter}();
            if ($value !== $currentValue) {
                $this->items[$id]->{$setter}($value);
                $this->updatedIds[] = $id;
            }
        }
    }

    public function getEditedItems(): array
    {
        $return = [];
        foreach ($this->updatedIds as $id) {
            if (isset($this->items[$id])) {
                $return[] = $this->items[$id];
            }
        }

        return $return;
    }
}

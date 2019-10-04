<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Data;

class ItemsSelection
{
    protected $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if (is_callable([$item, 'getId'])) {
                $this->items[$item->getId()] = false;
            }
        }
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(?array $items)
    {
        $this->items = $items;
    }
}

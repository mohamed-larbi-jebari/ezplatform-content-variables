<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Data;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Entity;

class ItemsSelection extends Collection
{
    protected $selectedIds = [];
    protected $action;
    protected $possibleActions = ['updatePriority', 'delete'];

    public function __construct(array $items = [])
    {
        parent::__construct($items);

        $ids = array_keys($this->items);
        foreach ($ids as $id) {
            $this->selectedIds[$id] = false;
        }

        $this->action = $this->possibleActions[0];
    }

    public function getSelectedId(): array
    {
        return $this->selectedIds;
    }

    public function setSelectedId(array $data): void
    {
        $this->selectedIds = $data;
    }

    public function getPriority(): array
    {
        return $this->getItemsProperty('priority');
    }

    public function setPriority(array $data): void
    {
        $this->setItemsProperty($data, 'priority');
    }

    /**
     * @return Entity[]
     */
    public function getSelectedItems(): array
    {
        $return = [];
        foreach ($this->getSelectedId() as $id => $isSelected) {
            if ($isSelected && isset($this->items[$id])) {
                $return[] = $this->items[$id];
            }
        }

        return $return;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function isActionValid(): bool
    {
        return in_array($this->getAction(), $this->possibleActions, true);
    }
}

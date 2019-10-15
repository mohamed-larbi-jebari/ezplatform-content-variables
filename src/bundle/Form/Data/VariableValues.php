<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Data;

class VariableValues extends Collection
{
    public function __construct(array $items = [])
    {
        foreach ($items as $variable) {
            $variable->setStaticValuePlaceholder();

            $this->items[$variable->getId()] = $variable;
        }
    }

    public function getValueStatic(): array
    {
        return $this->getItemsProperty('valueStatic');
    }

    public function setValueStatic(array $data): void
    {
        $this->setItemsProperty($data, 'valueStatic');
    }

    public function getValueCallback(): array
    {
        return $this->getItemsProperty('valueCallback');
    }

    public function setValueCallback(array $data): void
    {
        $this->setItemsProperty($data, 'valueCallback');
    }
}

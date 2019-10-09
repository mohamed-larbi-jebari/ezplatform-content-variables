<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Data;

class VariableValues
{
    protected $variables = [];
    protected $updatedIds = [];

    public function __construct(array $variables = [])
    {
        foreach ($variables as $variable) {
            $this->variables[$variable->getId()] = $variable;
        }
    }

    public function getEditedVariables(): array
    {
        $return = [];
        foreach ($this->updatedIds as $id) {
            if (isset($this->variables[$id])) {
                $return[] = $this->variables[$id];
            }
        }

        return $return;
    }

    public function getValueType(): array
    {
        return $this->getVariablesProperty('getValueType');
    }

    public function setValueType(?array $data): void
    {
        $this->setVariablesProperty($data, 'setValueType', 'getValueType');
    }

    public function getValueStatic(): array
    {
        return $this->getVariablesProperty('getValueStatic');
    }

    public function setValueStatic(?array $data): void
    {
        $this->setVariablesProperty($data, 'setValueStatic', 'getValueStatic');
    }

    public function getValueCallback(): array
    {
        return $this->getVariablesProperty('getValueCallback');
    }

    public function setValueCallback(?array $data): void
    {
        $this->setVariablesProperty($data, 'setValueCallback', 'getValueCallback');
    }
    
    protected function getVariablesProperty(string $getter): array
    {
        $return = [];
        foreach ($this->variables as $id => $variable) {
            $return[$id] = call_user_func([$variable, $getter]);
        }

        return $return;
    }

    protected function setVariablesProperty(?array $data, string $setter, string $getter): void
    {
        $return = [];
        foreach ($data as $id => $value) {
            if (isset($this->variables[$id]) === false) {
                continue;
            }

            $currentValue = call_user_func([$this->variables[$id], $getter]);
            if ($value !== $currentValue) {
                call_user_func([$this->variables[$id], $setter], $value);
                $this->updatedIds[] = $id;
            }
        }
    }
}

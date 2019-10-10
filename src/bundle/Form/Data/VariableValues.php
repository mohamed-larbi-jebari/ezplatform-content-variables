<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Data;

class VariableValues
{
    /** @var \ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable[] */
    protected $variables = [];
    /** @var int[] */
    protected $updatedIds = [];

    /**
     * @param \ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable[] $variables
     */
    public function __construct(array $variables = [])
    {
        foreach ($variables as $variable) {
            $this->variables[$variable->getId()] = $variable;
        }
    }

    /**
     * @return int[]
     */
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

    /**
     * @return int[]
     */
    public function getValueType(): array
    {
        return $this->getVariablesProperty('valueType');
    }

    public function setValueType(array $data): void
    {
        $this->setVariablesProperty($data, 'valueType');
    }

    /**
     * @return string[]
     */
    public function getValueStatic(): array
    {
        return $this->getVariablesProperty('valueStatic');
    }

    public function setValueStatic(array $data): void
    {
        $this->setVariablesProperty($data, 'valueStatic');
    }

    /**
     * @return string[]
     */
    public function getValueCallback(): array
    {
        return $this->getVariablesProperty('valueCallback');
    }

    public function setValueCallback(array $data): void
    {
        $this->setVariablesProperty($data, 'valueCallback');
    }

    /**
     * @param string $property
     * @return int[]|string[]
     */
    protected function getVariablesProperty(string $property): array
    {
        $getter = 'get' . ucfirst($property);

        $propertyValues = [];
        foreach ($this->variables as $id => $variable) {
            $propertyValues[$id] = $variable->{$getter}();
        }

        return $propertyValues;
    }

    /**
     * @param \ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable[] $data
     * @param string $property
     */
    protected function setVariablesProperty(array $data, string $property): void
    {
        $property = ucfirst($property);
        $getter = 'get' . $property;
        $setter = 'set' . $property;

        foreach ($data as $id => $value) {
            if (!isset($this->variables[$id])) {
                continue;
            }

            $currentValue = $this->variables[$id]->{$getter}();
            if ($value !== $currentValue) {
                $this->variables[$id]->{$setter}($value);
                $this->updatedIds[] = $id;
            }
        }
    }
}

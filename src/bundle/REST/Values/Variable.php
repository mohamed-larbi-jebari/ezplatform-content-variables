<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\Values;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable as VariableEntity;
use eZ\Publish\Core\REST\Common\Value as RestValue;

class Variable extends RestValue
{
    public $attributes = [
        'id' => null,
        'identifier' => null,
        'name' => null,
    ];

    public function __construct(VariableEntity $variable)
    {
        $this->attributes = [
            'id' => $variable->getId(),
            'identifier' => $variable->getIdentifier(),
            'name' => $variable->getName(),
        ];
    }
}

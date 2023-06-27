<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\Values;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection as CollectionEntity;
use Ibexa\Rest\Value as RestValue;

class Collection extends RestValue
{
    public $attributes = [
        'id' => null,
        'name' => null,
        'description' => null,
    ];
    /** @var Variable[] */
    public $variables = [];

    public function __construct(CollectionEntity $collection)
    {
        $this->attributes = [
            'id' => $collection->getId(),
            'name' => $collection->getName(),
            'description' => $collection->getDescription(),
        ];

        foreach ($collection->getContentVariables() as $variable) {
            $this->variables[] = new Variable($variable);
        }
    }
}

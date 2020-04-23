<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\Values;

use EzSystems\EzPlatformRest\Value as RestValue;

class CollectionList extends RestValue
{
    /** @var Collection[] */
    public $items = [];

    public function __construct(array $items)
    {
        foreach ($items as $item) {
            $this->items[] = new Collection($item);
        }
    }
}

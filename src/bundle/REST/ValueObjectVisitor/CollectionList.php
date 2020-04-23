<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\ValueObjectVisitor;

use EzSystems\EzPlatformRest\Output\Generator;
use EzSystems\EzPlatformRest\Output\ValueObjectVisitor;
use EzSystems\EzPlatformRest\Output\Visitor;

class CollectionList extends ValueObjectVisitor
{
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement('CollectionList');

        $generator->startList('Collection');
        foreach ($data->items as $collection) {
            $visitor->visitValueObject($collection);
        }
        $generator->endList('Collection');

        $generator->endObjectElement('CollectionList');
    }
}

<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

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

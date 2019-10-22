<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\ValueObjectVisitor;

use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Visitor;

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

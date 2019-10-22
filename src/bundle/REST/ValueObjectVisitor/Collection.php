<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\ValueObjectVisitor;

use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Visitor;

class Collection extends ValueObjectVisitor
{
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement('Collection');

        foreach ($data->attributes as $attribute => $value) {
            $generator->startAttribute($attribute, $value);
            $generator->endAttribute($attribute);
        }

        $generator->startList('Variables');
        foreach ($data->variables as $variable) {
            $visitor->visitValueObject($variable);
        }
        $generator->endList('Variables');

        $generator->endObjectElement('Collection');
    }
}

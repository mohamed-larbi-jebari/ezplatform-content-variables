<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\ValueObjectVisitor;

use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Visitor;

class Variable extends ValueObjectVisitor
{
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement('Variable');

        foreach ($data->attributes as $attribute => $value) {
            $generator->startAttribute($attribute, $value);
            $generator->endAttribute($attribute);
        }

        $generator->endObjectElement('Variable');
    }
}

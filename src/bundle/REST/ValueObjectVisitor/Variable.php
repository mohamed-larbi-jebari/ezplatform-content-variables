<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

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

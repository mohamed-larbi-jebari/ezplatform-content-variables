<?php

namespace ContextualCode\EzPlatformContentVariables\Variable\Value;

abstract class Callback
{
    /** @var string */
    protected $identifier;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getValue(): string
    {
        return '';
    }

    public function getName(): string
    {
        return $this->identifier;
    }
}

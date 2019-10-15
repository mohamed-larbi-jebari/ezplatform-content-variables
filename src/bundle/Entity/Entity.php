<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Entity;

abstract class Entity
{
    public function getId(): ?int
    {
        return null;
    }

    public function isNew(): bool
    {
        return $this->getId() === null;
    }

    public function canBeDeleted(): bool
    {
        return false;
    }
}

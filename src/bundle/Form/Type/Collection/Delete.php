<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Collection;

use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\ItemsDelete;

class Delete extends ItemsDelete
{
    protected function getDeleteLabel(): ?string
    {
        return 'collections_delete.delete';
    }
}

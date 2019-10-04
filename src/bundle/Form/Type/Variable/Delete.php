<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Variable;

use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\ItemsDelete;

class Delete extends ItemsDelete
{
    protected function getDeleteLabel(): ?string
    {
        return 'variables_delete.delete';
    }
}

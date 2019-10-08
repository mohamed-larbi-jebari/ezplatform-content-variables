<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Factory;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Collection\Delete as CollectionsDelete;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Collection\Edit as CollectionEdit;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Variable\Delete as VariablesDelete;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Variable\Edit as VariableEdit;

class FormFactory
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function collectionEdit(Collection $collection): FormInterface
    {
        return $this->formFactory->create(CollectionEdit::class, $collection);
    }

    public function collectionsDelete(ItemsSelection $data = null): FormInterface
    {
        return $this->formFactory->createNamed('collections_delete', CollectionsDelete::class, $data);
    }

    public function variablesEdit(Variable $variable): FormInterface
    {
        return $this->formFactory->create(VariableEdit::class, $variable, ['is_new' => $variable->isNew()]);
    }

    public function variablesDelete(ItemsSelection $data = null): FormInterface
    {
        return $this->formFactory->createNamed('variables_delete', VariablesDelete::class, $data);
    }
}

<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Factory;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\VariableValues;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Collection\BulkActions as CollectionsBulkActions;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Collection\Edit as CollectionEdit;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Variable\BulkActions as VariablesBulkActions;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Variable\BulkEdit as VariableBulkEdit;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Variable\Edit as VariableEdit;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Variable as VariableHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormFactory
{
    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var VariableHandler */
    protected $variableHandler;

    public function __construct(
        FormFactoryInterface $formFactory,
        VariableHandler $variableHandler
    ) {
        $this->formFactory = $formFactory;
        $this->variableHandler = $variableHandler;
    }

    public function collectionEdit(Collection $collection): FormInterface
    {
        return $this->formFactory->create(CollectionEdit::class, $collection);
    }

    public function collectionsBulkActions(array $collections = []): FormInterface
    {
        return $this->formFactory->createNamed(
            'collections_bulk_actions',
            CollectionsBulkActions::class,
            new ItemsSelection($collections)
        );
    }

    public function variablesEdit(Variable $variable): FormInterface
    {
        return $this->formFactory->create(
            VariableEdit::class,
            $variable,
            ['is_new' => $variable->isNew()]
        );
    }

    public function variablesBulkActions(array $variables): FormInterface
    {
        return $this->formFactory->createNamed(
            'variables_bulk_actions',
            VariablesBulkActions::class,
            new ItemsSelection($variables)
        );
    }

    public function variablesBulkEdit(array $variables): FormInterface
    {
        return $this->formFactory->create(VariableBulkEdit::class, new VariableValues($variables));
    }
}

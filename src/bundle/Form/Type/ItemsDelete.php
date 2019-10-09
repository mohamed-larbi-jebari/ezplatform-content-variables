<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Type;

use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ItemsDelete extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($this->getDeleteField(), CollectionType::class, [
                'entry_type' => CheckboxType::class,
                'required' => false,
                'allow_add' => true,
                'label' => false,
                'entry_options' => ['label' => false],
            ])
            ->add('delete', SubmitType::class, [
                'attr' => ['hidden' => true],
                'label' => $this->getDeleteLabel(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->getDataClass(),
            'translation_domain' => 'forms',
        ]);
    }

    protected function getDeleteField(): string
    {
        return 'items';
    }

    protected function getDataClass(): string
    {
        return ItemsSelection::class;
    }

    protected function getDeleteLabel(): ?string
    {
        return 'form.delete';
    }
}

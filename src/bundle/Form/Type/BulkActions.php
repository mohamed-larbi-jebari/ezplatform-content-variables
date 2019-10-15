<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Type;

use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BulkActions extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('selectedId', CollectionType::class, [
                'entry_type' => CheckboxType::class,
                'required' => false,
                'label' => false,
                'entry_options' => ['label' => false],
            ])
            ->add('priority', CollectionType::class, [
                'entry_type' => IntegerType::class,
                'label' => false,
                'entry_options' => ['label' => false],
            ])
            ->add('action', HiddenType::class, [
                'attr' => ['hidden' => true],
                'label' => false,
            ])
            ->add('do_action', SubmitType::class, [
                'attr' => ['hidden' => true],
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemsSelection::class,
            'translation_domain' => 'forms',
        ]);
    }
}

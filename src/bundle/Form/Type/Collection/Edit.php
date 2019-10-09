<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Collection;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection as DataClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Edit extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', null, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => DataClass::class]);
    }
}

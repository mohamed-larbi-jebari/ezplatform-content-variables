<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Collection;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection as DataClass;

class Edit extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', null, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => DataClass::class]);
    }
}
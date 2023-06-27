<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Form\Type\Variable;

use ContextualCode\EzPlatformContentVariables\Variable\Value\Processor;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable as DataClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Edit extends AbstractType
{
    /** @var Processor */
    protected $callbackProcessor;

    public function __construct(Processor $callbackProcessor)
    {
        $this->callbackProcessor = $callbackProcessor;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $callbacks = $this->callbackProcessor->getCallbackChoices();

        $builder
            ->add('name')
            ->add('identifier', null, ['attr' => [
                'readonly' => !$options['is_new'],
            ]]);

        if (count($callbacks) > 0) {
            $builder
                ->add('valueType', ChoiceType::class, [
                    'choices' => DataClass::getValueTypes(),
                    'choice_label' => static fn($choice, $key, $value) => 'variable.value_type.' . $value,
                    'choice_translation_domain' => 'content_variables',
                ])
                ->add('valueCallback', ChoiceType::class, ['choices' => $callbacks]);
        }

        $builder->add('valueStatic', null, ['required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('is_new');
        $resolver->setDefaults(['data_class' => DataClass::class]);
    }
}

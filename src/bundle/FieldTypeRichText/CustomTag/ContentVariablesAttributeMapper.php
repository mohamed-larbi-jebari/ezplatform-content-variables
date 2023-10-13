<?php

declare(strict_types=1);

namespace ContextualCode\EzPlatformContentVariablesBundle\FieldTypeRichText\CustomTag;

use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Variable;
use Ibexa\FieldTypeRichText\Configuration\UI\Mapper\CustomTag\CommonAttributeMapper;
use Ibexa\FieldTypeRichText\Configuration\UI\Mapper\CustomTag\AttributeMapper;
use Ibexa\FieldTypeRichText\Configuration\UI\Mapper\CustomTag\ChoiceAttributeMapper;

/**
 * Map RichText Custom Tag attribute of 'choice' type to proper UI config.
 *
 * @internal For internal use by RichText package
 */
final class ContentVariablesAttributeMapper extends CommonAttributeMapper implements AttributeMapper
{
    public function __construct(
        protected Collection $collectionHandler,
        protected Variable  $variableHandler,
        protected ChoiceAttributeMapper $choiceAttributeMapper
    ) {
    }
    public function supports(string $attributeType): bool
    {
        return 'choice' === $attributeType;
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(
        string $tagName,
        string $attributeName,
        array  $customTagAttributeProperties
    ): array
    {
        if($tagName !== 'content_variables') {
            return $this->choiceAttributeMapper->mapConfig($tagName, $attributeName, $customTagAttributeProperties);
        }
        $collections = $this->collectionHandler->findAll();
        $variables = $this->variableHandler->findAll();
        $collectionsNames = [];
        $choices = [];
        $choicesLabels = [];

        /** @var \ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable  $variable */
        foreach ($variables as $variable) {
            $collectionsNames[$variable->getCollection()?->getName()][] = $variable;
        }

        foreach ($collections as $collection) {
            $variables = $collectionsNames[$collection->getName()] ?? [];
            /** @var \ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable  $variable */
            foreach ($variables as $variable) {
                $label = $collection->getName() . ' :: '  . $variable->getName() . '[' . $variable->getIdentifier() . ']';
                $choices[] = $label;
                $choicesLabels[$label] = $variable->getIdentifier();
            }
        }
        $customTagAttributeProperties['choices'] = $choices;
        $parentConfig = parent::mapConfig($tagName, $attributeName, $customTagAttributeProperties);
        $parentConfig['choices'] = $customTagAttributeProperties['choices'];
        $parentConfig['choicesLabel'] = $choicesLabels;

        return $parentConfig;
    }
}

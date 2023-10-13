<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Entity;

use ContextualCode\EzPlatformContentVariablesBundle\EventSubscriber\ContentVariablesOutputFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="cc_content_variable")
 * @UniqueEntity("identifier")
 */
class Variable extends Entity
{
    private const VALUE_TYPE_STATIC = 1;
    private const VALUE_TYPE_CALLBACK = 2;
    private const VALUE_STATIC_PLACEHOLDER = 'empty-value-placeholder';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="contentVariables", fetch="EAGER")
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id")
     */
    private $collection;

    /**
     * @ORM\Column(type="string", length=256, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9_]+$/",
     *     message="variable.identifier"
     * )
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint", length=1, nullable=true)
     */
    private $valueType;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $valueStatic;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $valueCallback;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority = 0;

    /** @var int */
    private $linkedContentCount = 0;

    public function __construct()
    {
        $this->valueType = self::VALUE_TYPE_STATIC;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setCollection(?Collection $collection): void
    {
        $this->collection = $collection;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getValueType(): ?int
    {
        return $this->valueType;
    }

    public function setValueType(?int $valueType): void
    {
        $this->valueType = $valueType;
    }

    public function getValueStatic(): ?string
    {
        return $this->valueStatic;
    }

    public function setValueStatic(?string $valueStatic): void
    {
        $this->valueStatic = $valueStatic;
    }

    public function getValueCallback(): ?string
    {
        return $this->valueCallback;
    }

    public function setValueCallback(?string $valueCallback): void
    {
        $this->valueCallback = $valueCallback;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): void
    {
        $this->priority = $priority;
    }

    public function canBeDeleted(): bool
    {
        return $this->getLinkedContentCount() === 0;
    }

    public function isStatic(): bool
    {
        return $this->getValueType() === self::VALUE_TYPE_STATIC;
    }

    public function getLinkedContentCount(): int
    {
        return $this->linkedContentCount;
    }

    public function setLinkedContentCount(int $count): void
    {
        $this->linkedContentCount = $count;
    }

    public function getPlaceholder(): ?string
    {
        $identifier = $this->getIdentifier();
        if (!$identifier) {
            return null;
        }

        return ContentVariablesOutputFilter::WRAPPER . $identifier . ContentVariablesOutputFilter::WRAPPER;
    }

    public function setStaticValuePlaceholder(): void
    {
        if (
            $this->getValueType() === self::VALUE_TYPE_CALLBACK
            && empty($this->getValueStatic())
        ) {
            $this->setValueStatic(self::VALUE_STATIC_PLACEHOLDER);
        }
    }

    public function fixStaticValuePlaceholder(): void
    {
        if (
            $this->getValueType() === self::VALUE_TYPE_CALLBACK
            && $this->getValueStatic() === self::VALUE_STATIC_PLACEHOLDER
        ) {
            $this->setValueStatic(null);
        }
    }

    public function makeStatic(): void
    {
        $this->setValueType(self::VALUE_TYPE_STATIC);
        $this->setValueCallback(null);
    }

    public static function getValueTypes(): array
    {
        return [
            self::VALUE_TYPE_STATIC,
            self::VALUE_TYPE_CALLBACK,
        ];
    }
}

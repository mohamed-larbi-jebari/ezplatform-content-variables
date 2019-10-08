<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use ContextualCode\EzPlatformContentVariablesBundle\EventSubscriber\ContentVariablesOutputFilter;

/**
 * @ORM\Entity
 * @ORM\Table(name="cc_content_variable")
 * @UniqueEntity("identifier")
 */
class Variable implements ObjectManagerAware
{
    const VALUE_TYPE_STATIC = 1;
    const VALUE_TYPE_CALLBACK = 2;
    const VALUE_STATIC_PLACEHOLDER = 'empty-value-placeholder';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="contentVariables")
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id")
     */
    private $collection;

    /**
     * @ORM\Column(type="string", length=256, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[[:alnum:]_]+$/",
     *     message="variable.identifier"
     * )
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint", nullable=true)
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

    /** @var EntityManagerInterface */
    private $entityManager;

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

    public function isNew(): bool
    {
        return $this->getId() === null;
    }

    public function canBeDeleted(): bool
    {
        return $this->getLinkedContentCount() === 0;
    }

    public function isStatic(): bool
    {
        return $this->getValueType() === self::VALUE_TYPE_STATIC;
    }

    public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata)
    {
        $this->entityManager = $objectManager;
    }

    public function getLinkedContentCount(): int
    {
        $placeholder = $this->getPlaceholder();
        if ($placeholder === null) {
            return 0;
        }

        $query = $this->entityManager->getConnection()->createQueryBuilder()
            ->select('o.id, COUNT(o.id) as linked_objects_count')
            ->from('ezcontentobject', 'o')
            ->leftJoin(
                'o',
                'ezcontentobject_attribute',
                'a',
                '(a.version = o.current_version AND a.contentobject_id = o.id)'
            )
            ->where('a.data_text LIKE :contnet_variable')
            ->groupBy('o.id')
            ->setParameter('contnet_variable', '%' . $placeholder . '%');

        return (int) $query->execute()->rowCount();
    }

    public function getPlaceholder(): ?string
    {
        $identifier = $this->getIdentifier();
        if (empty($identifier)) {
            return null;
        }

        $separator = ContentVariablesOutputFilter::WRAPPER;
        return $separator . $identifier . $separator;
    }

    public static function getValueTypes(): array
    {
        return [
            self::VALUE_TYPE_STATIC,
            self::VALUE_TYPE_CALLBACK,
        ];
    }
}

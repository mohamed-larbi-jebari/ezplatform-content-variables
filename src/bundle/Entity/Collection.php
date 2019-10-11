<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as ItemsCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cc_content_variable_collection")
 */
class Collection
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Variable", mappedBy="collection")
     */
    private $contentVariables;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority;

    public function __construct()
    {
        $this->contentVariables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getContentVariables(): ItemsCollection
    {
        return $this->contentVariables;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): void
    {
        $this->priority = $priority;
    }

    public function isNew(): bool
    {
        return $this->getId() === null;
    }

    public function canBeDeleted(): bool
    {
        return count($this->getContentVariables()) === 0;
    }
}

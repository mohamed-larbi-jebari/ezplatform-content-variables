<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;

class CollectionHandler
{
    /** @var ObjectRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager
    ) {
        $this->repository = $doctrine->getRepository(Collection::class);
        $this->entityManager = $entityManager;
    }

    public function find(int $id): ?Collection
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function persist(Collection $collection): void
    {
        $this->entityManager->persist($collection);
        $this->entityManager->flush();
    }

    public function delete(Collection $collection): void
    {
        $this->entityManager->remove($collection);
        $this->entityManager->flush();
    }
}

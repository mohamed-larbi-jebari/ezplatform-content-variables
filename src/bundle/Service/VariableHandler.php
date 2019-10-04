<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable;

class VariableHandler
{
    /** @var ObjectRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager
    ) {
        $this->repository = $doctrine->getRepository(Variable::class);
        $this->entityManager = $entityManager;
    }

    public function find(int $id): ?Variable
    {
        return $this->repository->find($id);
    }

    public function findByCollection(Collection $collection): array
    {
        return $collection->getContentVariables()->getValues();
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function persist(Variable $variable): void
    {
        $this->entityManager->persist($variable);
        $this->entityManager->flush();
    }

    public function delete(Variable $variable): void
    {
        $this->entityManager->remove($variable);
        $this->entityManager->flush();
    }
}
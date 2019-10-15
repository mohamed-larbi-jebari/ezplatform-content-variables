<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Service\Handler;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Entity;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class Handler
{
    /** @var ObjectRepository */
    protected $repository;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager
    ) {
        $this->repository = $this->getRepository($doctrine);
        $this->entityManager = $entityManager;
    }

    protected function getRepository(ManagerRegistry $doctrine): ObjectRepository
    {
        return $doctrine->getRepository(Entity::class);
    }


    public function find(int $id): ?Entity
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findBy([], $this->getOrderBy());
    }

    public function persist(Entity $item): void
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    public function delete(Entity $item): void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }


    protected function getOrderBy(): array
    {
        return [
            'priority' => 'ASC',
            'id' => 'DESC',
        ];
    }
}

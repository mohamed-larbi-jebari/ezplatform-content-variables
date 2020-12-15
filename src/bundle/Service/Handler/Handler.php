<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Service\Handler;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Entity;
use Doctrine\Bundle\DoctrineBundle\Registry as ManagerRegistry;
use Doctrine\ORM\EntityRepository as ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class Handler
{
    /** @var ObjectRepository */
    protected $repository;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $this->getRepository();
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Entity::class);
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

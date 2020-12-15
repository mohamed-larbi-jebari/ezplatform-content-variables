<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Service\Handler;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection as CollectionEntity;
use Doctrine\Bundle\DoctrineBundle\Registry as ManagerRegistry;
use Doctrine\ORM\EntityRepository as ObjectRepository;

class Collection extends Handler
{
    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(CollectionEntity::class);
    }
}

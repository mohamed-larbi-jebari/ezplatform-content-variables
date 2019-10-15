<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Service\Handler;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection as CollectionEntity;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;

class Collection extends Handler
{
    protected function getRepository(ManagerRegistry $doctrine): ObjectRepository
    {
        return $doctrine->getRepository(CollectionEntity::class);
    }
}

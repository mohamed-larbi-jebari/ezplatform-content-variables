<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\REST\Controller;

use ContextualCode\EzPlatformContentVariablesBundle\REST\Values\CollectionList;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Collection as CollectionHandler;
use Exception;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Ibexa\Rest\Server\Controller as RestController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route(path: '/content_variable_collection', name: 'content_variables.rest.collection.')]
class Collection extends RestController
{
    /** @var CollectionHandler */
    protected $collectionHandler;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    public function __construct(
        CollectionHandler $collectionHandler,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->collectionHandler = $collectionHandler;
        $this->authorizationChecker = $authorizationChecker;

        $this->performSecurityAccessCheck();
    }

    public function performSecurityAccessCheck(): void
    {
        $attributes = new Attribute('content', 'manage_variables');
        if (!$this->authorizationChecker->isGranted($attributes)) {
            $exception = $this->createAccessDeniedException();
            $exception->setAttributes($attributes);

            throw $exception;
        }
    }

    protected function createAccessDeniedException(
        string $message = 'Access Denied.',
        Exception $previous = null
    ): AccessDeniedException {
        return new AccessDeniedException($message, $previous);
    }

    #[Route(path: '/list', name: 'list')]
    public function list(): CollectionList
    {
        return new CollectionList($this->collectionHandler->findAll());
    }
}

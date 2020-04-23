<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Controller;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Entity;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Factory\FormFactory;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Handler as EntityHandler;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Variable;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends Controller
{
    /** @var Collection */
    protected $collectionHandler;

    /** @var Variable */
    protected $variableHandler;

    /** @var FormFactory */
    protected $formFactory;

    /** @var NotificationHandlerInterface */
    protected $notificationHandler;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var SubmitHandler */
    protected $submitHandler;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var integer */
    protected $paginationLimit;

    /** @var string */
    protected $entityName;

    public function __construct(
        Collection $collectionHandler,
        Variable $variableHandler,
        FormFactory $formFactory,
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        SubmitHandler $submitHandler,
        AuthorizationCheckerInterface $authorizationChecker,
        UserSettingService $userSettingService
    ) {
        $this->collectionHandler = $collectionHandler;
        $this->variableHandler = $variableHandler;
        $this->formFactory = $formFactory;
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->submitHandler = $submitHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->paginationLimit = (int)$userSettingService->getUserSetting('subitems_limit')->value;

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

    protected function handleBulkAction(FormInterface $form): ?Response
    {
        $data = $form->getData();
        if (!$data->isActionValid()) {
            return null;
        }

        $actionHandler = $data->getAction() . 'Handler';
        $handler = [$this, $actionHandler];

        return is_callable($handler) ? $this->submitHandler->handle($form, $handler) : null;
    }

    public function updatePriorityHandler(ItemsSelection $data): ?Response
    {
        foreach ($data->getEditedItems() as $item) {
            $this->getEntityHandler()->persist($item);
            $this->sendSuccessMessage($item, 'priority_update');
        }

        return null;
    }

    public function deleteHandler(ItemsSelection $data): ?Response
    {
        foreach ($data->getSelectedItems() as $item) {
            if (!$item->canBeDeleted()) {
                continue;
            }

            $this->getEntityHandler()->delete($item);
            $this->sendSuccessMessage($item, 'delete');
        }

        return null;
    }

    protected function getEditMessage($item): string
    {
        return $this->getSuccessMessage($item, $item->isNew() ? 'new' : 'edit');
    }

    protected function sendSuccessMessage($item, string $action): void
    {
        $message = $this->getSuccessMessage($item, $action);
        $this->notificationHandler->success($message);
    }

    protected function getSuccessMessage(Entity $item, string $action): string
    {
        $key = $this->entityName . '.' . $action . '.success';
        $params = ['%name%' => $item->getName()];

        return $this->getTranslatedMessage($key, $params);
    }

    protected function getTranslatedMessage(string $key, array $params = []): string
    {
        return $this->translator->trans($key, $params, 'content_variables');
    }

    protected function getPagination(Request $request, array $items): Pagerfanta
    {
        $page = $request->query->get('page') ?? 1;

        $pagination = new Pagerfanta(
            new ArrayAdapter($items)
        );
        $pagination->setMaxPerPage($this->paginationLimit);
        $pagination->setCurrentPage(min($page, $pagination->getNbPages()));

        return $pagination;
    }

    abstract protected function getEntityHandler(): EntityHandler;
}

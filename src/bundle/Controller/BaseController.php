<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Controller;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Entity;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Factory\FormFactory;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Handler as EntityHandler;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Variable;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

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

    /** @var string */
    protected $entityName;

    public function __construct(
        Collection $collectionHandler,
        Variable $variableHandler,
        FormFactory $formFactory,
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        SubmitHandler $submitHandler
    ) {
        $this->collectionHandler = $collectionHandler;
        $this->variableHandler = $variableHandler;
        $this->formFactory = $formFactory;
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->submitHandler = $submitHandler;
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

    abstract protected function getEntityHandler(): EntityHandler;
}

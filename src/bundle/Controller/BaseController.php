<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Controller;

use Symfony\Component\Translation\TranslatorInterface;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Factory\FormFactory;
use ContextualCode\EzPlatformContentVariablesBundle\Service\CollectionHandler;
use ContextualCode\EzPlatformContentVariablesBundle\Service\VariableHandler;

abstract class BaseController extends Controller
{
    /** @var CollectionHandler */
    protected $collectionHandler;

    /** @var VariableHandler */
    protected $variableHandler;

    /** @var FormFactory */
    protected $formFactory;

    /** @var NotificationHandlerInterface */
    protected $notificationHandler;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var SubmitHandler */
    protected $submitHandler;

    public function __construct(
        CollectionHandler $collectionHandler,
        VariableHandler $variableHandler,
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

    protected function deleteHandler(ItemsSelection $data): void
    {
    }

    protected function getTranslatedMessage(string $key, array $params = []): string
    {
        return $this->translator->trans($key, $params, 'content_variables');
    }
}
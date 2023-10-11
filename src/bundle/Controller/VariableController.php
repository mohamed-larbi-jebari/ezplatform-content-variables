<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Controller;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\VariableValues;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Handler as EntityHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/content_variables', name: 'content_variables.')]
class VariableController extends BaseController
{
    private const BULK_EDIT_COLLAPSED_COLLECTIONS_COOKIE_VAR = 'content-variables_bulk-edit_collapsed-collections';
    private const BULK_EDIT_COLLAPSED_COLLECTIONS_COOKIE_SEPARATOR = ',';

    protected $entityName = 'variable';

    protected function getEntityHandler(): EntityHandler
    {
        return $this->variableHandler;
    }

    #[Route(path: '/{id}/variables', name: 'list', defaults: ['id' => null])]
    public function listAction(Request $request, Collection $collection): Response
    {
        $collectionVariables = $this->variableHandler->findByCollection($collection);
        $pagination = $this->getPagination($request, $collectionVariables);

        $variables = $pagination->getCurrentPageResults();
        foreach ($variables as $variable) {
            $this->variableHandler->countLinkedContent($variable);
        }

        $form = $this->formFactory->variablesBulkActions($variables);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->handleBulkAction($form);
            if ($result instanceof Response) {
                return $result;
            }

            return $this->redirectToRoute('content_variables.list', ['id' => $collection->getId()]);
        }

        $params = [
            'pager' => $pagination,
            'variables' => $variables,
            'collection' => $collection,
            'form' => $form->createView(),
        ];

        return $this->render('@ibexadesign/content_variable/variable/list.html.twig', $params);
    }

    #[Route(path: '/{id}/new', name: 'new', defaults: ['id' => null], requirements: ['id' => '\d+'])]
    public function createAction(Request $request, Collection $collection): Response
    {
        return $this->editAction($request, new Variable(), $collection);
    }

    #[Route(path: '/edit/{id}', name: 'edit', defaults: ['id' => null])]
    public function editAction(
        Request $request,
        Variable $variable,
        Collection $collection = null
    ): Response {
        if ($collection === null) {
            $collection = $variable->getCollection();
        }

        if ($this->variableHandler->missingCallbackToStatic($variable)) {
            $message = $this->getTranslatedMessage('variable.missing_callback.warning');
            $this->notificationHandler->warning($message);
        }

        $form = $this->formFactory->variablesEdit($variable);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $this->getEditMessage($variable);

            if ($variable->getCollection() === null) {
                $variable->setCollection($collection);
            }

            $this->variableHandler->persist($variable);
            $this->notificationHandler->success($message);

            return $this->redirectToRoute('content_variables.list', ['id' => $collection->getId()]);
        }

        $params = [
            'form' => $form->createView(),
            'variable' => $variable,
            'collection' => $collection,
        ];

        return $this->render('@ibexadesign/content_variable/variable/edit.html.twig', $params);
    }

    #[Route(path: '/{id}/linked_content', name: 'linked_content', defaults: ['id' => null], requirements: ['id' => '\d+'])]
    public function linkedContentAction(Variable $variable): Response
    {
        $linkedContentInfo = $this->variableHandler->linkedContentInfoGrouped($variable);

        $params = [
            'variable' => $variable,
            'linked_content' => $linkedContentInfo,
        ];

        return $this->render('@ibexadesign/content_variable/variable/related_content.html.twig', $params);
    }

    #[Route(path: '/bulk_edit', name: 'bulk_edit')]
    public function bulkEditAction(Request $request): Response
    {
        $collections = $this->collectionHandler->findAll();
        $variables = $this->variableHandler->findAll();
        $form = $this->formFactory->variablesBulkEdit($variables);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->submitHandler->handle($form, $this->bulkEditHandler(...));
            return $this->redirect(
                $this->generateUrl('content_variables.collection.list').'#ibexa-tab-second#tab'
            );
        }

        $params = [
            'collections' => $collections,
            'form' => $form->createView(),
            'collapsed_collections' => $this->getBulkEditCollapsedCollections($request),
        ];

        return $this->render('@ibexadesign/content_variable/variable/bulk_edit.html.twig', $params);
    }

    public function bulkEditHandler(VariableValues $data): void
    {
        foreach ($data->getEditedItems() as $variable) {
            $this->variableHandler->persist($variable);
            $this->sendSuccessMessage($variable, 'edit');
        }
    }

    protected function getBulkEditCollapsedCollections(Request $request): array
    {
        $var = self::BULK_EDIT_COLLAPSED_COLLECTIONS_COOKIE_VAR;
        $separator = self::BULK_EDIT_COLLAPSED_COLLECTIONS_COOKIE_SEPARATOR;

        $ids = explode($separator, $request->cookies->get($var, null));
        foreach ($ids as $k => $id) {
            $ids[$k] = (int) $id;
        }

        return [
            'ids' => $ids,
            'cookie_var' => $var,
            'cookie_separator' => $separator,
        ];
    }
}

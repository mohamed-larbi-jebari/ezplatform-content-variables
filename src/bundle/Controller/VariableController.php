<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Controller;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\VariableValues;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Handler as EntityHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/content_variables", name="content_variables.")
 */
class VariableController extends BaseController
{
    protected $entityName = 'variable';

    protected function getEntityHandler(): EntityHandler {
        return $this->variableHandler;
    }

    /**
     * @Route("/{id}/variables", name="list", defaults={"id"=null})
     */
    public function listAction(Request $request, Collection $collection): Response
    {
        $variables = $this->variableHandler->findByCollection($collection);
        $form = $this->formFactory->variablesBulkActions($collection);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->handleBulkAction($form);
            if ($result instanceof Response) {
                return $result;
            }

            return $this->redirectToRoute('content_variables.list', ['id' => $collection->getId()]);
        }

        $params = [
            'variables' => $variables,
            'collection' => $collection,
            'form' => $form->createView(),
        ];
        return $this->render('@ezdesign/content_variable/variable/list.html.twig', $params);
    }

    /**
     * @Route("/{id}/new", name="new", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function createAction(Request $request, Collection $collection): Response
    {
        return $this->editAction($request, new Variable(), $collection);
    }

    /**
     * @Route("/edit/{id}", name="edit", defaults={"id"=null})
     */
    public function editAction(
        Request $request,
        Variable $variable,
        Collection $collection = null
    ): Response {
        if ($collection === null) {
            $collection = $variable->getCollection();
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
        return $this->render('@ezdesign/content_variable/variable/edit.html.twig', $params);
    }

    /**
     * @Route("/{id}/linked_content", name="linked_content", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function linkedContentAction(Variable $variable): Response
    {
        $linkedContentInfo = $this->variableHandler->linkedContentInfo($variable);

        $params = [
            'variable' => $variable,
            'linked_content' => $linkedContentInfo,
        ];
        return $this->render('@ezdesign/content_variable/variable/related_content.html.twig', $params);
    }

    /**
     * @Route("/bulk_edit", name="bulk_edit")
     */
    public function bulkEditAction(Request $request): Response
    {
        $collections = $this->collectionHandler->findAll();
        $variables = $this->variableHandler->findAll();
        $form = $this->formFactory->variablesBulkEdit($variables);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, [$this, 'bulkEditHandler']);
            if ($result instanceof Response) {
                return $result;
            }
        }

        $params = [
            'collections' => $collections,
            'form' => $form->createView(),
        ];
        return $this->render('@ezdesign/content_variable/variable/bulk_edit.html.twig', $params);
    }

    public function bulkEditHandler(VariableValues $data): void
    {
        foreach ($data->getEditedItems() as $variable) {
            $this->variableHandler->persist($variable);
            $this->sendSuccessMessage($variable, 'edit');
        }
    }
}
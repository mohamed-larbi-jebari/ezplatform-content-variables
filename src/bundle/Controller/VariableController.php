<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;

class VariableController extends BaseController
{
    /**
     * @Route("/content_variables/{id}/new", name="content_variables.new", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function createAction(Request $request, Collection $collection): Response
    {
        return $this->editAction($request, new Variable(), $collection);
    }

    /**
     * @Route("/content_variables/edit/{id}", name="content_variables.edit", defaults={"id"=null})
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
     * @Route("/content_variables/{id}/variables", name="content_variables.list", defaults={"id"=null})
     */
    public function listAction(Collection $collection)
    {
        $variables = $this->variableHandler->findByCollection($collection);
        $form = $this->formFactory->variablesDelete(new ItemsSelection($variables));

        $params = [
            'variables' => $variables,
            'collection' => $collection,
            'form' => $form->createView(),
        ];
        return $this->render('@ezdesign/content_variable/variable/list.html.twig', $params);
    }

    /**
     * @Route("/content_variables/{id}/bulk_delete", name="content_variables.bulk_delete", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function bulkDeleteAction(Request $request, Collection $collection): Response
    {
        $form = $this->formFactory->variablesDelete();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, [$this, 'deleteHandler']);
            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('content_variables.list', ['id' => $collection->getId()]);
    }

    /**
     * @Route("/content_variables/{id}/linked_content", name="content_variables.linked_content", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function linkedContentAction(Request $request, Variable $variable): Response
    {
        $linkedContentInfo = $this->variableHandler->linkedContentInfo($variable);

        $params = [
            'variable' => $variable,
            'linked_content' => $linkedContentInfo,
        ];
        return $this->render('@ezdesign/content_variable/variable/related_content.html.twig', $params);
    }

    public function deleteHandler(ItemsSelection $data): void
    {
        foreach ($data->getItems() as $variableId => $selected) {
            if ($selected === false) {
                continue;
            }

            $variable = $this->variableHandler->find($variableId);
            $this->variableHandler->delete($variable);

            $params = ['%name%' => $variable->getName()];
            $message = $this->getTranslatedMessage('variable.delete.success', $params);
            $this->notificationHandler->success($message);
        }
    }

    protected function getEditMessage(Variable $variable): string
    {
        $key = $variable->isNew() ? 'variable.new.success' : 'variable.edit.success';
        $params = ['%name%' => $variable->getName()];
        return $this->getTranslatedMessage($key, $params);
    }
}
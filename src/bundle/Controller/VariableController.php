<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Controller;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/content_variables", name="content_variables.")
 */
class VariableController extends BaseController
{
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
     * @Route("/{id}/variables", name="list", defaults={"id"=null})
     */
    public function listAction(Collection $collection): Response
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
     * @Route("/{id}/bulk_delete", name="bulk_delete", defaults={"id"=null}, requirements={"id"="\d+"})
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

    public function deleteHandler(ItemsSelection $data): void
    {
        foreach ($data->getItems() as $variableId => $selected) {
            if ($selected === false) {
                continue;
            }

            $variable = $this->variableHandler->find($variableId);
            if ($variable) {
                $this->variableHandler->delete($variable);
            }

            $message = $this->getTranslatedMessage('variable.delete.success', [
                '%name%' => $variable ? $variable->getName() : $variableId,
            ]);
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

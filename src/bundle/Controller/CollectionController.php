<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Controller;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Form\Data\ItemsSelection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/content_variables/collection", name="content_variables.collection.")
 */
class CollectionController extends BaseController
{
    /**
     * @Route("/new", name="new")
     */
    public function createAction(Request $request): Response
    {
        return $this->editAction($request, new Collection());
    }

    /**
     * @Route("/edit/{id}", name="edit", defaults={"id"=null})
     */
    public function editAction(Request $request, Collection $collection): Response
    {
        $form = $this->formFactory->collectionEdit($collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $this->getEditMessage($collection);

            $this->collectionHandler->persist($collection);
            $this->notificationHandler->success($message);

            return $this->redirectToRoute('content_variables.collection.list');
        }

        $params = [
            'collection' => $collection,
            'form' => $form->createView(),
        ];

        return $this->render('@ezdesign/content_variable/collection/edit.html.twig', $params);
    }

    /**
     * @Route("/list", name="list")
     */
    public function listAction(): Response
    {
        $collections = $this->collectionHandler->findAll();
        $form = $this->formFactory->collectionsDelete(new ItemsSelection($collections));

        $params = [
            'collections' => $collections,
            'form' => $form->createView(),
        ];

        return $this->render('@ezdesign/content_variable/collection/list.html.twig', $params);
    }

    /**
     * @Route("/bulk_delete", name="bulk_delete")
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->collectionsDelete();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, [$this, 'deleteHandler']);
            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('content_variables.collection.list');
    }

    public function deleteHandler(ItemsSelection $data): void
    {
        foreach ($data->getItems() as $collectionId => $selected) {
            if ($selected === false) {
                continue;
            }

            $collection = $this->collectionHandler->find($collectionId);
            if ($collection) {
                $this->collectionHandler->delete($collection);
            }

            $message = $this->getTranslatedMessage('collection.delete.success', [
                '%name%' => $collection ? $collection->getName() : $collectionId,
            ]);
            $this->notificationHandler->success($message);
        }
    }

    protected function getEditMessage(Collection $collection): string
    {
        $key = $collection->isNew() ? 'collection.new.success' : 'collection.edit.success';
        $params = ['%name%' => $collection->getName()];

        return $this->getTranslatedMessage($key, $params);
    }
}

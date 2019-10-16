<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Controller;

use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Handler as EntityHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/content_variables/collection", name="content_variables.collection.")
 */
class CollectionController extends BaseController
{
    protected $entityName = 'collection';

    protected function getEntityHandler(): EntityHandler
    {
        return $this->collectionHandler;
    }

    /**
     * @Route("/list", name="list")
     */
    public function listAction(Request $request): Response
    {
        $collections = $this->collectionHandler->findAll();
        $form = $this->formFactory->collectionsBulkActions($collections);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->handleBulkAction($form);
            if ($result instanceof Response) {
                return $result;
            }

            return $this->redirectToRoute('content_variables.collection.list');
        }

        $params = [
            'collections' => $collections,
            'form' => $form->createView(),
        ];

        return $this->render('@ezdesign/content_variable/collection/list.html.twig', $params);
    }

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
}

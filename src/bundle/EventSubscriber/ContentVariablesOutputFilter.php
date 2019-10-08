<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use ContextualCode\EzPlatformContentVariablesBundle\Service\VariableHandler;


class ContentVariablesOutputFilter implements EventSubscriberInterface
{
    const WRAPPER = '#';

    /** @var VariableHandler */
    protected $variableHandler;

    public function __construct(VariableHandler $variableHandler) {
        $this->variableHandler = $variableHandler;
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        if (
            $response instanceof StreamedResponse ||
            $response instanceof RedirectResponse ||
            $response instanceof JsonResponse
        ) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');
        if (in_array($route, $this->getSupportedRoutes()) === false) {
            return;
        }

        $content = $response->getContent();
        $response->setContent($this->replaceContentVariables($content));
    }

    protected function getSupportedRoutes(): array
    {
        return [
            'ez_urlalias',
        ];
    }

    public function replaceContentVariables(string $content): string
    {
        $variables = $this->variableHandler->findAll();
        foreach ($variables as $variable) {
            $placeholder = $variable->getPlaceholder();
            if ($placeholder === null || strpos($content, $placeholder) === false) {
                continue;
            }

            if ($variable->getLinkedContentCount() === 0) {
                continue;
            }

            $value = $this->variableHandler->getVariableValue($variable);
            if ($value === null) {
                continue;
            }

            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -5],
        ];
    }
}
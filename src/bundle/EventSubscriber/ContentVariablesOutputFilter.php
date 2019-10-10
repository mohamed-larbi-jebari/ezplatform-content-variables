<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\EventSubscriber;

use ContextualCode\EzPlatformContentVariablesBundle\Service\VariableHandler;
use eZ\Bundle\EzPublishIOBundle\BinaryStreamResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ContentVariablesOutputFilter implements EventSubscriberInterface
{
    public const WRAPPER = '#';

    /** @var VariableHandler */
    protected $variableHandler;

    public function __construct(VariableHandler $variableHandler)
    {
        $this->variableHandler = $variableHandler;
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        if (
            $response instanceof StreamedResponse
            || $response instanceof RedirectResponse
            || $response instanceof JsonResponse
            || $response instanceof BinaryFileResponse
            || $response instanceof BinaryStreamResponse
        ) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');
        if (!in_array($route, $this->getSupportedRoutes(), true)) {
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

        $replacementFrom = [];
        $replacementTo = [];
        foreach ($variables as $variable) {
            $placeholder = $variable->getPlaceholder();
            if ($placeholder === null || strpos($content, $placeholder) === false) {
                continue;
            }

            $value = $this->variableHandler->getVariableValue($variable);
            if ($value === null) {
                continue;
            }
            $replacementFrom[] = $placeholder;
            $replacementTo[] = $value;
        }
        if ($replacementFrom) {
            $content = str_replace($replacementFrom, $replacementTo, $content);
        }

        return $content;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -5],
        ];
    }
}

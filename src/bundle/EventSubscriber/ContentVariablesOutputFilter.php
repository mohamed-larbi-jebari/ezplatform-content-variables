<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\EventSubscriber;

use ContextualCode\EzPlatformContentVariablesBundle\Service\Handler\Variable;
use eZ\Bundle\EzPublishIOBundle\BinaryStreamResponse;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ContentVariablesOutputFilter implements EventSubscriberInterface
{
    public const WRAPPER = '#';

    /** @var Variable */
    protected $variableHandler;

    /** @var ConfigResolverInterface */
    protected $configResolver;

    /** @var string */
    protected $fragmentPath;

    public function __construct(string $fragmentPath, Variable $variableHandler, ConfigResolverInterface $configResolver)
    {
        $this->variableHandler = $variableHandler;
        $this->configResolver = $configResolver;
        $this->fragmentPath = $fragmentPath;
    }

    public function onKernelResponse(ResponseEvent $event): void
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

        $request = $event->getRequest();
        $isFragment = ($this->fragmentPath === rawurldecode($request->getPathInfo()));

        $supportedRoutes = $this->getSupportedRoutes();
        if (\count($supportedRoutes) > 0 && !$isFragment) {
            $route = $request->attributes->get('_route');
            if (!in_array($route, $this->getSupportedRoutes(), true)) {
                return;
            }
        }

        $content = $response->getContent();
        $response->setContent($this->replaceContentVariables($content));
    }

    protected function getSupportedRoutes(): array
    {
        return array_unique(($this->configResolver->getParameter('supported_routes', 'ez_platform_content_variables')));
    }

    public function replaceContentVariables(string $content): string
    {
        /** @var \ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable[] $variables */
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

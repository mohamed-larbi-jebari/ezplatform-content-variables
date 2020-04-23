<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\DependencyInjection\CompilerPass;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RichTextXslPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $scopes = array_merge(
            [ConfigResolver::SCOPE_DEFAULT],
            $container->getParameter('ezpublish.siteaccess.list')
        );
        $configs = [
            // 'input_custom_xsl' => ['xhtml5/input/content_variable.xsl'], - disabled while full override in place
            'edit_custom_xsl' => ['xhtml5/edit/content_variable.xsl'],
            'output_custom_xsl' => ['xhtml5/output/content_variable.xsl'],
        ];

        foreach ($scopes as $scope) {
            foreach ($configs as $type => $extraRules) {
                $this->addCustomXsl($container, $scope, $type, $extraRules);
            }
        }
    }

    private function addCustomXsl(
        ContainerBuilder $container,
        string $scope,
        string $type,
        array $rules
    ): void {
        $parameter = "ezsettings.{$scope}.fieldtypes.ezrichtext.{$type}";
        if (!$container->hasParameter($parameter)) {
            return;
        }

        $extraRules = [];
        foreach ($rules as $rule) {
            $extraRules[] = [
                'path' => __DIR__ . '/../../Resources/xsl/' . $rule,
                'priority' => 250,
            ];
        }

        $newRules = array_merge(
            $container->getParameter($parameter),
            $extraRules
        );
        $container->setParameter($parameter, $newRules);
    }
}
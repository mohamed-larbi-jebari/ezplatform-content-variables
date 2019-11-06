<?php

namespace ContextualCode\EzPlatformContentVariablesBundle;

use ContextualCode\EzPlatformContentVariablesBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzPlatformContentVariablesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $ezpublish = $container->getExtension('ezpublish');
        $ezpublish->addPolicyProvider(new Security\ContentVariables());

        $container->addCompilerPass(new CompilerPass\RichTextXslPass());
    }
}

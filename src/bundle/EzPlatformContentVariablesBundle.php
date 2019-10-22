<?php

namespace ContextualCode\EzPlatformContentVariablesBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzPlatformContentVariablesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $ezpublish = $container->getExtension('ezpublish');
        $ezpublish->addPolicyProvider(new Security\ContentVariables());
    }
}

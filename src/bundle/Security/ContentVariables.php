<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Security;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class ContentVariables extends YamlPolicyProvider
{
    protected function getFiles()
    {
        return [
            __DIR__ . '/../Resources/config/policies.yaml',
        ];
    }
}

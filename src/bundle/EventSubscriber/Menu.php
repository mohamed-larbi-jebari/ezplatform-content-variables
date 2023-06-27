<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\EventSubscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Menu implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', 0],
        ];
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $menu[MainMenuBuilder::ITEM_CONTENT]->addChild(
            'content_variables',
            [
                'label' => 'content_variables.menu.title',
                'route' => 'content_variables.collection.list',
                'extras' => [
                    'routes' => [
                        'content_variables.collection.new',
                        'content_variables.collection.edit',
                        'content_variables.bulk_edit',
                        'content_variables.new',
                        'content_variables.edit',
                        'content_variables.list',
                        'content_variables.linked_content',
                    ],
                ],
            ]
        );
    }
}

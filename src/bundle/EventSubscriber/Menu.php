<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\EventSubscriber;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Menu implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', 0],
        ];
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $label = $this->translator->trans('collection.list', [], 'content_variables');
        $menu[MainMenuBuilder::ITEM_CONTENT]->addChild(
            'content_variables',
            [
                'label' => $label,
                'route' => 'content_variables.collection.list',
                'extras' => [
                    'routes' => [
                        'content_variables.collection.new',
                        'content_variables.collection.edit',
                        'content_variables.new',
                        'content_variables.edit',
                        'content_variables.list',
                    ],
                ],
            ]
        );
    }
}

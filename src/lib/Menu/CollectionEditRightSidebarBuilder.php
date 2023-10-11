<?php

namespace ContextualCode\EzPlatformContentVariables\Menu;

use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

class CollectionEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    private const ITEM__SAVE = 'content_variable_collection_edit__sidebar_right__save';
    private const ITEM__CANCEL = 'content_variable_collection_edit__sidebar_right__cancel';

    protected function getConfigureEventName(): string
    {
        return 'content_variables.menu_configure.collection_edit_sidebar_right';
    }

    protected function createStructure(array $options): ItemInterface
    {
        /** @var ItemInterface $menu */
        $menu = $this->factory->createItem('root');

        $menu->setChildren([
            self::ITEM__SAVE => $this->createMenuItem(
                self::ITEM__SAVE,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => '#collection-update',
                    ],
                    'extras' => ['icon' => 'save'],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'route' => 'content_variables.collection.list',
                    'extras' => ['icon' => 'circle-close'],
                ]
            ),
        ]);

        return $menu;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__SAVE, 'menu')),
            (new Message(self::ITEM__CANCEL, 'menu')),
        ];
    }
}

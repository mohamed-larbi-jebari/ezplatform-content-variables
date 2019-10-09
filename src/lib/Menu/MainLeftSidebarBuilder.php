<?php

namespace ContextualCode\EzPlatformContentVariables\Menu;

use EzSystems\EzPlatformAdminUi\Menu\AbstractBuilder;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

class MainLeftSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    private const ITEM__BULK_EDIT = 'content_variable_main__sidebar_left__bulk_edit';
    private const ITEM__COLLECTIONS = 'content_variable_main__sidebar_left__collections';

    protected function getConfigureEventName(): string
    {
        return 'content_variables.menu_configure.main_sidebar_left';
    }

    protected function createStructure(array $options): ItemInterface
    {
        /** @var ItemInterface|ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $menu->setChildren([
            self::ITEM__COLLECTIONS => $this->createMenuItem(
                self::ITEM__COLLECTIONS,
                [
                    'route' => 'content_variables.collection.list',
                    'extras' => ['icon' => 'sections'],
                ]
            ),
            self::ITEM__BULK_EDIT => $this->createMenuItem(
                self::ITEM__BULK_EDIT,
                [
                    'route' => 'content_variables.bulk_edit',
                    'extras' => ['icon' => 'edit'],
                ]
            ),
        ]);

        return $menu;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__BULK_EDIT, 'menu')),
            (new Message(self::ITEM__COLLECTIONS, 'menu')),
        ];
    }
}

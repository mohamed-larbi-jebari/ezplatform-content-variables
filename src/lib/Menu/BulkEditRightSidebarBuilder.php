<?php

namespace ContextualCode\EzPlatformContentVariables\Menu;

use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\Attribute\Required;

class BulkEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    protected RouterInterface $router;
    private const ITEM__SAVE = 'content_variable_bulk_edit__sidebar_right__save';
    private const ITEM__CANCEL = 'content_variable_bulk_edit__sidebar_right__cancel';

    #[Required]
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }
    protected function getConfigureEventName(): string
    {
        return 'content_variables.menu_configure.bulk_edit_sidebar_right';
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
                        'data-click' => '#variables-update',
                    ],
                    'extras' => ['icon' => 'save'],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'uri' =>  $this->router->generate('content_variables.collection.list', ['reset' => time()]).'#ibexa-tab-second#tab',
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
        ];
    }
}

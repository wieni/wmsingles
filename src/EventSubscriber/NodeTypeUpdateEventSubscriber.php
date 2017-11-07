<?php

namespace Drupal\wmsingles\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wmSingles\Service\WmSingles;
use Drupal\hook_event_dispatcher\Event\Entity\BaseEntityEvent;
use Drupal\hook_event_dispatcher\HookEventDispatcherEvents;

/**
 * Class NodeTypeUpdateEventSubscriber
 * @package Drupal\wmSingles\EventSubscriber
 */
class NodeTypeUpdateEventSubscriber implements EventSubscriberInterface
{
    /** @var wmSingles */
    private $wmSingles;

    /**
     * EntityEventSubscriber constructor.
     * @param wmSingles $wmSingles
     */
    public function __construct(
        wmSingles $wmSingles
    ) {
        $this->wmSingles = $wmSingles;
    }

    /**
     * @param BaseEntityEvent $event
     */
    public function checkForSingles(BaseEntityEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof NodeTypeInterface) {
            $this->wmSingles->checkSingle($entity);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            HookEventDispatcherEvents::ENTITY_UPDATE => [
                ['checkForSingles'],
            ],
            HookEventDispatcherEvents::ENTITY_INSERT => [
                ['checkForSingles'],
            ],
        ];
    }
}

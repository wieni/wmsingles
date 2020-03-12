<?php

namespace Drupal\wmsingles\EventSubscriber;

use Drupal\hook_event_dispatcher\Event\Entity\BaseEntityEvent;
use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wmsingles\Service\WmSinglesInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NodeTypeUpdateEventSubscriber implements EventSubscriberInterface
{
    /** @var WmSinglesInterface */
    private $wmSingles;

    public function __construct(
        WmSinglesInterface $wmSingles
    ) {
        $this->wmSingles = $wmSingles;
    }

    public function checkForSingles(BaseEntityEvent $event): void
    {
        $entity = $event->getEntity();

        if ($entity instanceof NodeTypeInterface) {
            $this->wmSingles->checkSingle($entity);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            HookEventDispatcherInterface::ENTITY_UPDATE => [['checkForSingles']],
            HookEventDispatcherInterface::ENTITY_INSERT => [['checkForSingles']],
        ];
    }
}

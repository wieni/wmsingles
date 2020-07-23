<?php

namespace Drupal\wmsingles\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wmsingles\Service\WmSinglesInterface;

class NodeTypeUpdateEventSubscriber
{
    /** @var WmSinglesInterface */
    private $wmSingles;

    public function __construct(
        WmSinglesInterface $wmSingles
    ) {
        $this->wmSingles = $wmSingles;
    }

    public function checkForSingles(EntityInterface $entity): void
    {
        if ($entity instanceof NodeTypeInterface) {
            $this->wmSingles->checkSingle($entity);
        }
    }
}

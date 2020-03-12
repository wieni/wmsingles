<?php

namespace Drupal\wmsingles\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wmsingles\Service\WmSinglesInterface;

class NodeAddAccess implements AccessInterface
{
    /** @var RouteMatchInterface */
    protected $currentRoute;
    /** @var WmSinglesInterface */
    protected $wmSingles;

    public function __construct(
        WmSinglesInterface $wmSingles,
        RouteMatchInterface $currentRoute
    ) {
        $this->wmSingles = $wmSingles;
        $this->currentRoute = $currentRoute;
    }

    public function access(): AccessResultInterface
    {
        /** @var NodeTypeInterface $type */
        $type = $this->currentRoute->getParameter('node_type');

        if ($type && $this->wmSingles->isSingle($type)) {
            return AccessResult::forbidden();
        }

        return AccessResult::allowed();
    }
}

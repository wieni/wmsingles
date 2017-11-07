<?php

namespace Drupal\wmsingles\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wmSingles\Service\WmSingles;
use Drupal\node\Entity\NodeType;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Class NodeAddAccess
 * @package Drupal\wmSingles\Access
 */
class NodeAddAccess implements AccessInterface
{
    /** @var  CurrentRouteMatch */
    private $currentRoute;

    /** @var  WmSingles */
    private $wmSingles;

    /**
     * NodeDeleteAccess constructor.
     * @param WmSingles $wmSingles
     */
    public function __construct(
        WmSingles $wmSingles,
        CurrentRouteMatch $currentRoute
    ) {
        $this->wmSingles = $wmSingles;
        $this->currentRoute = $currentRoute;
    }

    /**
     * @param NodeInterface $node
     * @return \Drupal\Core\Access\AccessResultForbidden|\Drupal\Core\Access\AccessResultNeutral
     */
    public function access()
    {
        /** @var NodeTypeInterface $type */
        $type = $this->currentRoute->getParameter('node_type');

        if ($type && $this->wmSingles->isSingle($type)) {
            return AccessResult::forbidden();
        }

        return AccessResult::allowed();
    }
}

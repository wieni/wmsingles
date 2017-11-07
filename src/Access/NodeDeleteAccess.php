<?php

namespace Drupal\wmsingles\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wmSingles\Service\WmSingles;
use Drupal\node\Entity\NodeType;
use Drupal\Core\Session\AccountInterface;

/**
 * Class NodeDeleteAccess
 * @package Drupal\wmSingles\Access
 */
class NodeDeleteAccess implements AccessInterface
{
    /** @var  WmSingles */
    private $wmSingles;

    /**
     * NodeDeleteAccess constructor.
     * @param WmSingles $wmSingles
     */
    public function __construct(
        WmSingles $wmSingles
    ) {
        $this->wmSingles = $wmSingles;
    }

    /**
     * @param NodeInterface $node
     * @param AccountInterface $account
     * @return \Drupal\Core\Access\AccessResultAllowed|\Drupal\Core\Access\AccessResultForbidden|\Drupal\Core\Access\AccessResultNeutral
     */
    public function access(NodeInterface $node, AccountInterface $account)
    {
        if ($account->hasPermission('administer wmsingles')) {
            return AccessResult::allowed();
        }

        /** @var NodeTypeInterface $type */
        $type = NodeType::load($node->bundle());

        if ($this->wmSingles->isSingle($type)) {
            return AccessResult::forbidden();
        }

        return AccessResult::allowed();
    }
}

<?php

namespace Drupal\wmsingles\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wmsingles\Service\WmSinglesInterface;

class NodeDeleteAccess implements AccessInterface
{
    /** @var WmSinglesInterface */
    protected $wmSingles;

    public function __construct(
        WmSinglesInterface $wmSingles
    ) {
        $this->wmSingles = $wmSingles;
    }

    public function access(NodeInterface $node, AccountInterface $account): AccessResultInterface
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

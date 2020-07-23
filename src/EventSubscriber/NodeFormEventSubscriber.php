<?php

namespace Drupal\wmsingles\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\wmsingles\Service\WmSinglesInterface;

class NodeFormEventSubscriber
{
    /** @var RouteMatchInterface */
    protected $currentRouteMatch;
    /** @var WmSinglesInterface */
    protected $wmSingles;

    public function __construct(
        RouteMatchInterface $currentRouteMatch,
        WmSinglesInterface $wmSingles
    ) {
        $this->currentRouteMatch = $currentRouteMatch;
        $this->wmSingles = $wmSingles;
    }

    public function formAlter(array &$form): void
    {
        if ($this->currentRouteMatch->getRouteName() !== 'entity.node.edit_form') {
            return;
        }

        $node = $this->currentRouteMatch->getParameter('node');
        if (empty($node) || !$this->wmSingles->getSingleByBundle($node->bundle())) {
            return;
        }

        if (isset($form['meta']['author'])) {
            $form['meta']['author']['#access'] = false;
            $form['meta']['published']['#access'] = false;
        }
    }
}

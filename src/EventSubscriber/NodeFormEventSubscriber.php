<?php

namespace Drupal\wmsingles\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\hook_event_dispatcher\Event\Form\BaseFormEvent;
use Drupal\wmsingles\Service\WmSinglesInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NodeFormEventSubscriber implements EventSubscriberInterface
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

    public function formAlter(BaseFormEvent $event): void
    {
        $form = &$event->getForm();

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

    public static function getSubscribedEvents()
    {
        return [
            'hook_event_dispatcher.form_base_node_form.alter' => 'formAlter',
        ];
    }
}

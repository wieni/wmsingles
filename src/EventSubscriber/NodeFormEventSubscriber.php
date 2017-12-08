<?php

namespace Drupal\wmsingles\EventSubscriber;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\hook_event_dispatcher\Event\Form\BaseFormEvent;
use Drupal\wmsingles\Service\WmSingles;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\hook_event_dispatcher\Event\Form\FormBaseAlterEvent;
use Drupal\node\NodeTypeInterface;

/**
 * Class NodeFormEventSubscriber
 * @package Drupal\wmSingles\EventSubscriber
 */
class NodeFormEventSubscriber implements EventSubscriberInterface
{
    /** @var CurrentRouteMatch */
    private $currentRouteMatch;
    /** @var WmSingles */
    private $wmSingles;

    /**
     * NodeFormEventSubscriber constructor.
     * @param CurrentRouteMatch $currentRouteMatch
     * @param WmSingles $wmSingles
     */
    public function __construct($currentRouteMatch, $wmSingles)
    {
        $this->currentRouteMatch = $currentRouteMatch;
        $this->wmSingles = $wmSingles;
    }

    /**
     * @param BaseFormEvent $event
     */
    public function formAlter(BaseFormEvent $event)
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

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'hook_event_dispatcher.form_base_node_form.alter' => 'formAlter',
        ];
    }
}

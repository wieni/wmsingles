<?php

namespace Drupal\wmsingles\EventSubscriber;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\hook_event_dispatcher\Event\Form\BaseFormEvent;
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

    /**
     * NodeFormEventSubscriber constructor.
     * @param CurrentRouteMatch $currentRouteMatch
     */
    public function __construct($currentRouteMatch)
    {
        $this->currentRouteMatch = $currentRouteMatch;
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

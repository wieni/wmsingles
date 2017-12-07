<?php

namespace Drupal\wmsingles\EventSubscriber;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\hook_event_dispatcher\Event\Form\FormBaseAlterEvent;
use Drupal\node\NodeTypeInterface;

/**
 * Class NodeTypeFormEventSubscriber
 * @package Drupal\wmSingles\EventSubscriber
 */
class NodeTypeFormEventSubscriber implements EventSubscriberInterface
{
    /**
     * @param \Drupal\hook_event_dispatcher\Event\Form\FormIdAlterEvent $event
     */
    public function alterNodeTypeForm(FormBaseAlterEvent $event)
    {
        $form = &$event->getForm();

        /** @var FormStateInterface $form_state */
        $formState = $event->getFormState();

        /** @var \Drupal\node\NodeTypeInterface $type */
        $type = $formState->getFormObject()->getEntity();
        $form['wmsingles'] = [
            '#type' => 'details',
            '#title' => t('Singles'),
            '#group' => 'additional_settings',
        ];
        $form['wmsingles']['is-single'] = [
            '#type' => 'checkbox',
            '#title' => t('This is a content type with a single entity.'),
            '#default_value' => $type->getThirdPartySetting('wmsingles', 'isSingle', false),
            '#description' => t('The entity will be created after you save this content type.'),
        ];

        $form['#entity_builders'][] = [$this, 'wmSinglesFormNodeTypeFormBuilder'];
    }

    /**
     * Entity builder for the node type form with wmsingles checkbox.
     *
     * @see alterNodeTypeForm()
     */
    public static function wmSinglesFormNodeTypeFormBuilder(
        $entity_type,
        NodeTypeInterface $type,
        &$form,
        FormStateInterface $form_state
    ) {
        $type->setThirdPartySetting('wmsingles', 'isSingle', $form_state->getValue('is-single'));
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'hook_event_dispatcher.form_base_node_type_form.alter' => [
                ['alterNodeTypeForm'],
            ],
        ];
    }
}

<?php

namespace Drupal\wmsingles\EventSubscriber;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeTypeInterface;

class NodeTypeFormEventSubscriber
{
    use StringTranslationTrait;

    public function alterNodeTypeForm(array &$form, FormStateInterface $formState): void
    {
        /** @var NodeTypeInterface $type */
        $type = $formState->getFormObject()->getEntity();

        $form['wmsingles'] = [
            '#type' => 'details',
            '#title' => $this->t('Singles'),
            '#group' => 'additional_settings',
        ];

        $form['wmsingles']['is-single'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('This is a content type with a single entity.'),
            '#default_value' => $type->getThirdPartySetting('wmsingles', 'isSingle', false),
            '#description' => $this->t('The entity will be created after you save this content type.'),
        ];

        $form['#entity_builders'][] = [static::class, 'formBuilder'];
    }

    public static function formBuilder($entity_type, NodeTypeInterface $type, &$form, FormStateInterface $form_state): void
    {
        $type->setThirdPartySetting('wmsingles', 'isSingle', $form_state->getValue('is-single'));
    }
}

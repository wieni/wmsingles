<?php

/**
 * @file
 * Builds placeholder replacement tokens for wmsingles
 */

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\node\Entity\NodeType;

/**
 * Implements hook_token_info().
 *
 * @return array
 */
function wmsingles_token_info()
{
    $types['wmsingles'] = [
        'name' => t("WmSingles"),
        'description' => t("WmSingles tokens"),
    ];

    // Get all singles.
    $wmSingles = [];
    /** @var EntityTypeInterface $type */
    foreach (\Drupal::service('wmsingles')->getAllSingles() as $type) {
        $wmSingles[$type->id() . ':url'] = [
            'name' => $type->label() . ' URL',
            'description' => t('The Alias of the single.')
        ];
    }

    return [
        'types' => $types,
        'tokens' => [
            'wmsingles' => $wmSingles,
        ],
    ];
}

/**
 * Implements hook_tokens().
 *
 * @return array
 */
function wmsingles_tokens($type, $tokens, array $data = [], array $options = [])
{
    $replacements =[];

    if ($type == 'wmsingles') {
        foreach ($tokens as $name => $original) {
            $split = explode(':', $name);

            // Load the content type
            $node = \Drupal::service('wmsingles')->getSingleByBundle($split[0]);
            if ($node) {
                // Failsafe
                $replace = '';

                if ($split[1] == 'url') {
                    $canonical = '/node/' . $node->id();
                    $alias = \Drupal::service('path.alias_manager')->getAliasByPath($canonical);
                    if ($alias !== $canonical) {
                        $replace = $alias;
                    }
                }

                $replacements[$original] = $replace;
            }
        }
    }

    return $replacements;
}

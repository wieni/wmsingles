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
            switch ($name) {
                case 'ledennetwerk:url':
                    $typeString = explode(':', $name);
                    /** @var EntityTypeInterface $type */
                    $type = NodeType::load($typeString[0]);

                    $replacements[$original] = '';

                    if ($type instanceof NodeType) {
                        /** @var \Drupal\node\NodeInterface $node */
                        if ($node = \Drupal::service('wmsingles')->getSingle($type)) {
                            $canonical = '/node/' . $node->id();
                            $alias = \Drupal::service('path.alias_manager')->getAliasByPath($canonical);

                            if ($alias !== $canonical) {
                                $replacements[$original] = $alias;
                            }
                        }
                    }
                    break;
            }
        }
    }

    return $replacements;
}

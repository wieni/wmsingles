<?php

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\wmsingles\Service\WmSinglesInterface;

function wmsingles_token_info()
{
    $types['wmsingles'] = [
        'name' => t('WmSingles'),
        'description' => t('WmSingles tokens'),
    ];

    // Get all singles.
    $wmSingles = [];
    /** @var EntityTypeInterface $type */
    foreach (\Drupal::service('wmsingles')->getAllSingles() as $type) {
        $wmSingles[$type->id() . ':url'] = [
            'name' => $type->label() . ' URL',
            'description' => t('The Alias of the single.'),
        ];
    }

    return [
        'types' => $types,
        'tokens' => [
            'wmsingles' => $wmSingles,
        ],
    ];
}

function wmsingles_tokens($type, $tokens, array $data = [], array $options = [])
{
    $replacements = [];

    if ($type !== 'wmsingles') {
        return $replacements;
    }

    /** @var WmSinglesInterface $wmSingles */
    $wmSingles = \Drupal::service('wmsingles');
    /** @var AliasManagerInterface $pathAliasManager */
    $pathAliasManager = \Drupal::service('path.alias_manager');

    foreach ($tokens as $name => $original) {
        $split = explode(':', $name);

        // Load the content type
        $node = $wmSingles->getSingleByBundle($split[0]);

        if (!$node instanceof NodeInterface) {
            continue;
        }

        if ($split[1] === 'url') {
            $canonical = '/node/' . $node->id();
            $alias = $pathAliasManager->getAliasByPath($canonical, $options['langcode']);

            if ($alias !== $canonical) {
                $replace = $alias;
            }
        }

        $replacements[$original] = $replace ?? '';
    }

    return $replacements;
}

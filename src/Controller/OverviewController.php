<?php

namespace Drupal\wmsingles\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeTypeInterface;
use Drupal\wmsingles\Service\WmSinglesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OverviewController implements ContainerInjectionInterface
{
    use StringTranslationTrait;

    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var WmSinglesInterface */
    protected $wmSingles;

    public static function create(ContainerInterface $container)
    {
        $instance = new static;
        $instance->entityTypeManager = $container->get('entity_type.manager');
        $instance->wmSingles = $container->get('wmsingles');

        return $instance;
    }

    public function overview()
    {
        $output['table'] = [
            '#type' => 'table',
            '#header' => [
                $this->t('Name'),
                $this->t('Type'),
                $this->t('Description'),
                $this->t('Operations'),
            ],
            '#empty' => $this->t('No singles found.'),
            '#sticky' => true,
        ];

        /** @var NodeTypeInterface $item */
        foreach ($this->wmSingles->getAllSingles() as $item) {
            $node = $this->wmSingles->getSingleByBundle($item->id());

            if ($node) {
                $nodeType = NodeType::load($node->bundle());
                $operations = $this->entityTypeManager->getListBuilder('node')->getOperations($node);

                $output['table'][$item->id()]['title'] = [
                    '#markup' => sprintf(
                        '<a href="%s">%s</a>',
                        Url::fromRoute('entity.node.canonical', ['node' => $node->id()])->toString(),
                        $node->label()
                    ),
                ];

                $output['table'][$item->id()]['bundle'] = [
                    '#plain_text' => $nodeType->label(),
                ];

                $output['table'][$item->id()]['description'] = [
                    '#plain_text' => $item->getDescription(),
                ];

                $output['table'][$item->id()]['operations'] = [
                    '#type' => 'operations',
                    '#subtype' => 'node',
                    '#links' => $operations,
                ];
            }
        }

        return $output;
    }
}

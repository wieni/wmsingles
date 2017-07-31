<?php

namespace Drupal\wmsingles\Controller;

use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wmcontroller\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\wmSingles\Service\WmSingles;

/**
 * Class OverviewController
 * @package Drupal\wmsingles\Controller
 */
class OverviewController extends ControllerBase
{
    /**
     * @var WmSingles
     */
    protected $wmSingles;

    /**
     * OverviewController constructor.
     * @param WmSingles $wmSingles
     */
    public function __construct(
        WmSingles $wmSingles
    ) {
        $this->wmSingles = $wmSingles;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @return static
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('wmsingles')
        );
    }

    /**
     * @return mixed
     */
    public function overview()
    {
        $output['table'] = [
            '#type' => 'table',
            '#header' => [
                $this->t('Title'),
                $this->t('Type'),
                $this->t('Operations'),
            ],
            '#empty' => $this->t('No singles found.'),
            '#sticky' => true,
        ];

        /** @var NodeTypeInterface $item */
        foreach ($this->wmSingles->getAllSingles() as $item) {
            /** @var NodeInterface $node */
            $node = $this->wmSingles->getSingleByBundle($item->id());
            if ($node) {
                $operations = $this->entityTypeManager()->getListBuilder('node')->getOperations($node);

                $output['table'][$item->id()]['title'] = [
                    '#markup' => $node->label(),
                ];

                $output['table'][$item->id()]['type'] = [
                    '#markup' => $item->label(),
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

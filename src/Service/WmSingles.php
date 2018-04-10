<?php

namespace Drupal\wmsingles\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\node\Entity\NodeType;
use Drupal\Core\State\StateInterface;

class WmSingles
{

    /**
     * The entity type manager.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * The state.
     *
     * @var \Drupal\Core\State\StateInterface
     */
    protected $state;

    /**
     * The language manager.
     *
     * @var \Drupal\Core\Language\LanguageManagerInterface
     */
    protected $languageManager;

    /**
     * The config.
     *
     * @var \Drupal\Core\Config\Config
     */
    private $config;

    /**
     * Constructs a WmContentManageAccessCheck object.
     *
     * @param EntityTypeManagerInterface $entityTypeManager
     * @param StateInterface $state
     * @param LanguageManagerInterface $languageManager
     * @param ConfigFactoryInterface $configFactory
     */
    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        StateInterface $state,
        LanguageManagerInterface $languageManager,
        ConfigFactoryInterface $configFactory
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->state = $state;
        $this->languageManager = $languageManager;
        $this->config = $configFactory->get('wmsingles');
    }

    /**
     * This functions checks that for each key there is a corresponding
     * entity in the given bundle, and creates one if it's not there.
     * @param NodeTypeInterface $type
     * @throws \Exception
     */
    public function checkSingle(NodeTypeInterface $type)
    {
        if ($this->isSingle($type)) {
            /** @var QueryInterface $query */
            $query = $this->entityTypeManager->getStorage('node')->getQuery();
            $snowFlakeCount = $query
                ->condition('type', $type->id())
                ->count()
                ->execute();

            if ($snowFlakeCount == 0) {
                $entity = $this->createNode($type);
                $this->setSnowFlake($type, $entity);
            } elseif ($snowFlakeCount > 1) {
                throw new \Exception('Single Bundle with more then one entity.');
            }
        }
    }

    /**
     * Returns a loaded single node.
     *
     * @param NodeTypeInterface $type
     * @return bool|\Drupal\Core\Entity\EntityInterface|null
     */
    public function getSingle(NodeTypeInterface $type)
    {
        $id = $this->getSnowFlake($type);

        if (!$id) {
            return null;
        }

        $node = $this->loadNode($id);

        if (empty($node)) {
            $this->deleteSnowFlake($type);
            $this->checkSingle($type);

            return $this->getSingle($type);
        }

        return $node;
    }

    /**
     * @param $bundle
     * @return bool|\Drupal\Core\Entity\EntityInterface|null
     */
    public function getSingleByBundle($bundle)
    {
        $types = $this->getAllSingles();
        return isset($types[$bundle]) ? $this->getSingle($types[$bundle]) : null;
    }

    /**
     * Check whether a bundle is single or not.
     *
     * @param NodeTypeInterface $type
     * @return bool
     */
    public function isSingle(NodeTypeInterface $type)
    {
        return $type->getThirdPartySetting('wmsingles', 'isSingle', false);
    }

    /**
     * Get all single content types.
     * @return array|mixed
     */
    public function getAllSingles()
    {
        $list = &drupal_static(__FUNCTION__);
        if (!isset($list)) {
            $list = [];
            /** @var NodeTypeInterface $type */
            foreach (NodeType::loadMultiple() as $type) {
                if ($this->isSingle($type)) {
                    $list[$type->get('type')] = $type;
                }
            }
        }
        return $list;
    }

    /**
     * Set the snowflake entity id for a single bundle.
     *
     * @param NodeTypeInterface $type
     * @param NodeInterface $node
     */
    public function setSnowFlake(NodeTypeInterface $type, NodeInterface $node)
    {
        $this->state->set($this->getSnowFlakeKey($type), (int) $node->id());
    }

    /**
     * Delete the snowflake entity id for a single bundle.
     *
     * @param NodeTypeInterface $type
     */
    public function deleteSnowFlake(NodeTypeInterface $type)
    {
        $this->state->delete($this->getSnowFlakeKey($type));
    }

    /**
     * Get the current snowflake id for a single bundle.
     * @param NodeTypeInterface $type
     * @return integer
     */
    public function getSnowFlake(NodeTypeInterface $type)
    {
        return $this->state->get($this->getSnowFlakeKey($type), 0);
    }

    private function getSnowFlakeKey(NodeTypeInterface $type)
    {
        return 'wmsingles.' . $type->id();
    }

    /**
     * Create a node to be used for a single content type
     * @param NodeTypeInterface $type
     * @return NodeInterface
     */
    protected function createNode(NodeTypeInterface $type)
    {
        /** @var NodeInterface $entity */
        $entity = $this
            ->entityTypeManager
            ->getStorage('node')
            ->create([
                'type' => $type->id(),
                'title' => $type->label(),
                'path' =>  ['alias' => '/' . $type->id()]
            ]);
        $entity->save();

        return $entity;
    }

    private function loadNode($id)
    {
        $langcode = $this->languageManager->getCurrentLanguage()->getId();
        /** @var NodeInterface $single */
        $single = $this->entityTypeManager->getStorage('node')->load($id);

        $strictTranslation = $this->config->get('strict_translation');

        if (!$single || !$single->hasTranslation($langcode)) {
            return $strictTranslation ? null : $single;
        }

        return $single->getTranslation($langcode);
    }
}

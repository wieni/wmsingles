<?php

namespace Drupal\wmsingles\Service;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\node\Entity\NodeType;
use Drupal\Core\State\StateInterface;

class WmSingles
{
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var StateInterface */
    protected $state;
    /** @var LanguageManagerInterface */
    protected $languageManager;
    /** @var Config */
    private $config;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        StateInterface $state,
        LanguageManagerInterface $languageManager,
        ConfigFactoryInterface $configFactory
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->state = $state;
        $this->languageManager = $languageManager;
        $this->config = $configFactory->get('wmsingles.settings');
    }

    /**
     * This functions checks that for each key there is a corresponding
     * entity in the given bundle, and creates one if it's not there.
     *
     * @param NodeTypeInterface $type
     * @throws \Exception
     */
    public function checkSingle(NodeTypeInterface $type)
    {
        if (!$this->isSingle($type)) {
            return;
        }

        $entity = null;
        $storage = $this->entityTypeManager->getStorage('node');
        $nodes = $storage->getQuery()
            ->condition('type', $type->id())
            ->execute();

        // There are multiple nodes, this shouldn't happen
        if (count($nodes) > 1) {
            throw new \Exception('Single Bundle with more then one entity.');
        }

        // There aren't any nodes yet, so create one
        if (empty($nodes)) {
            $entity = $this->createNode($type);
        }

        $snowFlake = $this->getSnowFlake($type);
        $node = reset($nodes);

        // There's 1 node, but no snowflake (or a snowflake that doesn't match the nid)
        if ($node !== $snowFlake) {
            $entity = $storage->load($node);
        }

        // There's 1 node, but its id is not yet stored as snowflake
        if (!$this->getSnowFlake($type)) {
            $entity = $storage->load(reset($nodes));
        }

        if ($entity instanceof NodeInterface) {
            $this->setSnowFlake($type, $entity);
        }
    }

    /**
     * Returns a loaded single node.
     *
     * @param NodeTypeInterface $type
     * @return NodeInterface|null
     * @throws \Exception
     */
    public function getSingle(NodeTypeInterface $type)
    {
        $tries = 0;

        do {
            $tries++;
            $id = $this->getSnowFlake($type);
            $node = $this->loadNode($id);

            if (!$node instanceof NodeInterface) {
                $this->checkSingle($type);
            }
        } while ($tries < 2);

        return $node;
    }

    /**
     * @param $bundle
     * @return NodeInterface|null
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
        $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
        /** @var NodeInterface $single */
        $single = $this->entityTypeManager->getStorage('node')->load($id);

        if ($single->hasTranslation($langcode)) {
            return $single->getTranslation($langcode);
        }

        if ($single->get('langcode')->value === $langcode || !$this->config->get('strict_translation')) {
            return $single;
        }

        return null;
    }
}

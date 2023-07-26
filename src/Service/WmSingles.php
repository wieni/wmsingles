<?php

namespace Drupal\wmsingles\Service;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;

class WmSingles implements WmSinglesInterface
{
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var StateInterface */
    protected $state;
    /** @var LanguageManagerInterface */
    protected $languageManager;
    /** @var Config */
    protected $config;

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

    public function checkSingle(NodeTypeInterface $type): void
    {
        if (!$this->isSingle($type)) {
            return;
        }

        $entity = null;
        $storage = $this->entityTypeManager->getStorage('node');
        $nodes = $storage->getQuery()
            ->condition('type', $type->id())
            ->accessCheck(false)
            ->execute();

        // There are multiple nodes, this shouldn't happen
        if (count($nodes) > 1) {
            throw new \Exception('Single Bundle with more then one entity.');
        }

        // There aren't any nodes yet, so create one
        if (empty($nodes)) {
            $entity = $this->createNode($type);
        }

        // There's 1 node, but no snowflake (or a snowflake that doesn't match the nid)
        if (count($nodes) === 1) {
            $snowFlake = $this->getSnowFlake($type);
            $node = reset($nodes);

            if ($node !== $snowFlake) {
                $entity = $storage->load($node);
            }
        }

        if ($entity instanceof NodeInterface) {
            $this->setSnowFlake($type, $entity);
        }
    }

    public function getSingle(NodeTypeInterface $type, ?string $langcode = null): ?NodeInterface
    {
        $langcode = $langcode ?? $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
        $tries = 0;

        do {
            $tries++;
            $id = $this->getSnowFlake($type);

            if (!$id) {
                $this->checkSingle($type);
            }

            $node = $this->loadNode($id, $langcode);

            if (!$node instanceof NodeInterface) {
                $this->checkSingle($type);
            }
        } while ($tries < 2);

        return $node;
    }

    public function getSingleByBundle(string $bundle, ?string $langcode = null): ?NodeInterface
    {
        $types = $this->getAllSingles();

        return isset($types[$bundle])
            ? $this->getSingle($types[$bundle], $langcode) :
            null;
    }

    public function isSingle(NodeTypeInterface $type): bool
    {
        return $type->getThirdPartySetting('wmsingles', 'isSingle', false);
    }

    public function getAllSingles(): array
    {
        $list = &drupal_static(__FUNCTION__);

        if (isset($list)) {
            return $list;
        }

        $list = [];
        /** @var NodeTypeInterface $type */
        foreach (NodeType::loadMultiple() as $type) {
            if ($this->isSingle($type)) {
                $list[$type->get('type')] = $type;
            }
        }

        return $list;
    }

    protected function setSnowFlake(NodeTypeInterface $type, NodeInterface $node): void
    {
        $this->state->set($this->getSnowFlakeKey($type), (int) $node->id());
    }

    protected function getSnowFlake(NodeTypeInterface $type): ?int
    {
        return $this->state->get($this->getSnowFlakeKey($type));
    }

    protected function getSnowFlakeKey(NodeTypeInterface $type): string
    {
        return 'wmsingles.' . $type->id();
    }

    protected function createNode(NodeTypeInterface $type): NodeInterface
    {
        /** @var NodeInterface $entity */
        $entity = $this
            ->entityTypeManager
            ->getStorage('node')
            ->create([
                'type' => $type->id(),
                'title' => $type->label(),
                'path' => ['alias' => '/' . $type->id()],
            ]);
        $entity->save();

        return $entity;
    }

    protected function loadNode(string $id, string $langcode)
    {
        /** @var NodeInterface $single */
        $single = $this->entityTypeManager->getStorage('node')->load($id);

        if (!$single instanceof NodeInterface) {
            return null;
        }

        if ($single->hasTranslation($langcode)) {
            return $single->getTranslation($langcode);
        }

        if ($single->get('langcode')->value === $langcode || !$this->config->get('strict_translation')) {
            return $single;
        }

        return null;
    }
}

<?php

namespace Drupal\wmsingles\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeAccessControlHandler;
use Drupal\wmsingles\Service\WmSinglesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SingleNodeAccessControlHandler extends NodeAccessControlHandler
{
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var WmSinglesInterface */
    protected $singles;

    public static function createInstance(ContainerInterface $container, EntityTypeInterface $entityType)
    {
        $instance = parent::createInstance($container, $entityType);
        $instance->entityTypeManager = $container->get('entity_type.manager');
        $instance->singles = $container->get('wmsingles');

        return $instance;
    }

    public function access(EntityInterface $entity, $operation, ?AccountInterface $account = null, $return_as_object = false)
    {
        $account = $this->prepareUser($account);
        $isSingle = $this->singles->isSingle($entity->type->entity);

        if ($isSingle && $operation === 'delete' && !$account->hasPermission('administer wmsingles')) {
            $result = AccessResult::forbidden('Singles cannot be deleted manually.')->cachePerPermissions();

            return $return_as_object ? $result : $result->isAllowed();
        }

        $result = parent::access($entity, $operation, $account, true);

        return $return_as_object ? $result : $result->isAllowed();
    }

    public function createAccess($entity_bundle = null, ?AccountInterface $account = null, array $context = [], $return_as_object = false)
    {
        $nodeType = $this->entityTypeManager
            ->getStorage('node_type')
            ->load($entity_bundle);

        if ($this->singles->isSingle($nodeType)) {
            $result = AccessResult::forbidden('Singles can only be created once, automatically');

            return $return_as_object ? $result : $result->isAllowed();
        }

        $result = parent::createAccess($entity_bundle, $account, $context, $return_as_object);

        return $return_as_object ? $result : $result->isAllowed();
    }
}

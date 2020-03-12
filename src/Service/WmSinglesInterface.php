<?php

namespace Drupal\wmsingles\Service;

use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;

interface WmSinglesInterface
{
    /**
     * Checks that for each key there is a corresponding
     * entity in the given bundle, and creates one if it's not there.
     */
    public function checkSingle(NodeTypeInterface $type): void;

    /** Returns a loaded single node by node type. */
    public function getSingle(NodeTypeInterface $type, ?string $langcode = null): ?NodeInterface;

    /** Returns a loaded single node by node type ID. */
    public function getSingleByBundle(string $bundle, ?string $langcode = null): ?NodeInterface;

    /** Check whether a node type is single or not. */
    public function isSingle(NodeTypeInterface $type): bool;

    /** Get all single content types. */
    public function getAllSingles(): array;
}

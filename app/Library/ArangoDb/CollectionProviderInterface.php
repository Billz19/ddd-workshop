<?php


namespace App\Library\ArangoDb;

/**
 * CollectionProviderInterface is a collection information provider to use with @ArangoDbInitializer
 */
interface CollectionProviderInterface
{
    /**
     * Retuns name of the collection.
     */
    public function getName(): string;

    /**
     * Returns array indexex
     * if collection not has any index then return empty array.
     */
    public function getIndexes(): array;
}

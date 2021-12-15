<?php


namespace App\Packages\Posts\Repository\Arango;

use App\Library\ArangoDb\CollectionProviderInterface;
use ArangoDBClient\CollectionHandler;

/**
 * @PostsCollection describes the collection for the @Post model.
 */
class PostsCollection implements CollectionProviderInterface
{
    public const COLLECTION = "posts";

    /**
     * @inheritdoc
     */
    public function getName(): string { return static::COLLECTION; }

    /**
     * @inheritdoc
     */
    public function getIndexes(): array
    {
        return [
            [
                'name'                           => 'title_unique_index',
                CollectionHandler::OPTION_FIELDS => ['title'],
                CollectionHandler::OPTION_TYPE   => CollectionHandler::OPTION_PERSISTENT_INDEX,
                CollectionHandler::OPTION_UNIQUE => true,
            ],
        ];
    }
}

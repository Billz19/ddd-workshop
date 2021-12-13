<?php


namespace App\Packages\Users\Repository\Arango;

use App\Library\ArangoDb\CollectionProviderInterface;
use ArangoDBClient\CollectionHandler;

/**
 * @UsersCollection describes the collection for the @User model.
 */
class UsersCollection implements CollectionProviderInterface
{
    public const COLLECTION = "users";

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
                'name'                           => 'email_unique_index',
                CollectionHandler::OPTION_FIELDS => ['email'],
                CollectionHandler::OPTION_TYPE   => CollectionHandler::OPTION_PERSISTENT_INDEX,
                CollectionHandler::OPTION_UNIQUE => true,
            ],
        ];
    }
}

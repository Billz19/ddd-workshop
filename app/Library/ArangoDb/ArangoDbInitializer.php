<?php


namespace App\Library\ArangoDb;

use App\Packages\Exceptions\UnknownDBErrorException;
use ArangoDBClient\CollectionHandler as ArangoCollectionHandler;
use ArangoDBClient\Connection as ArangoConnection;
use ArangoDBClient\Database as ArangoDatabase;
use Exception;

/**
 * @InitArangoDb handle creation of arango databases.
 */
class ArangoDbInitializer
{
    public function __construct(
        private ArangoConnection $connection,
        private array $collections,
    )
    {
    }

    /**
     * Initialize arangodb database collections.
     *
     * @throws @DbErrorException
     */
    public function init(string $dbName = '_system'): void
    {
        try{
            $this->createDatabase($dbName);

            foreach($this->collections as $collection) {
                $this->createCollection($collection);
            }
        }
        catch(Exception $e) {
            throw new UnknownDBErrorException(message: "could not initialize the database '$dbName'", previous: $e);
        }
    }

    /**
     * Create database sent in params $dbName if not exists.
     */
    private function createDatabase(string $dbName): void
    {
        // adminConnection is connection with "_system" as datbase name, required for database handling
        $adminConnection = $this->connection;
        $adminConnection->setDatabase('_system');
        $databases       = ArangoDatabase::databases($adminConnection);

        if(! in_array($dbName,$databases['result'] )) {
            ArangoDatabase::create($adminConnection, $dbName);
        }
        $this->connection->setDatabase($dbName);
    }

    /**
     * Create new arango collection if collection not exists.
     */
    private function createCollection(CollectionProviderInterface $collection): void
    {
        $collectionHandler = new ArangoCollectionHandler($this->connection);
        if (! $collectionHandler->has($collection->getName())) {
            $collectionHandler->create($collection->getName());
            $this->createIndexes($collectionHandler, $collection);
        }
    }

    /**
     * Create the collection indexes.
     */
    private function createIndexes(ArangoCollectionHandler $collectionHandler, CollectionProviderInterface $collection): void
    {
        foreach($collection->getIndexes() as $index) {
            $collectionHandler->createIndex($collection->getName(), $index);
        }
    }
}

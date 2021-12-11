<?php

namespace Tests\Unit\App\Library\ArangoDb;

use ArangoDBClient\Connection as ArangoConnection;
use Illuminate\Support\Facades\Config;
use App\Library\ArangoDb\CollectionProviderInterface;
use ArangoDBClient\Database as ArangoDatabase;
use App\Library\ArangoDb\ArangoDbInitializer;
use Tests\Helpers\ArangoConnectionTrait;
use ArangoDBClient\CollectionHandler;
use Tests\TestCase;

class ArangoDbInitializerTest extends TestCase
{
    private CollectionProviderInterface $testCollection;
    private const DATABASE_NAME  = 'test_create_database';
    public const COLLECTION_NAME = 'test_collection';
    public const INDEX_NAME      = 'test_field_unique_index';
    public const FIELD_NAME      = 'test_field';
    public const OPTION_NAME     = 'name';

    protected function setUp(): void
    {
        parent::setUp();
        // after the environment has been loaded we need to delete any test database before connection get initialized
        $this->deleteTestDatabase();
        $this->testCollection = new class implements CollectionProviderInterface {
            public function getName(): string { return ArangoDbInitializerTest::COLLECTION_NAME; }

            public function getIndexes(): array
            {
                return [
                    [
                        ArangoDbInitializerTest::OPTION_NAME => ArangoDbInitializerTest::INDEX_NAME,
                        CollectionHandler::OPTION_FIELDS     => [ArangoDbInitializerTest::FIELD_NAME],
                        CollectionHandler::OPTION_TYPE       => CollectionHandler::OPTION_PERSISTENT_INDEX,
                        CollectionHandler::OPTION_UNIQUE     => true,
                    ],
                ];
            }
        };
    }

    /**
     * @after
     */
    public function deleteTestDatabase(): void
    {
        $connection   = $this->getArangoConnection();
        $databases = $this->getDatabases();
        if(in_array(static::DATABASE_NAME, $databases)) {
            ArangoDatabase::delete($connection, static::DATABASE_NAME);
        }
    }

    /**
     * @test
     */
    public function init()
    {
        $connection   = $this->getArangoConnection();
        $initArangoDb = new ArangoDbInitializer($connection, [$this->testCollection]);
        $initArangoDb->init(static::DATABASE_NAME);
        $databases    = $this->getDatabases();
        $index        = $this->getIndex();

        // assert database is created successfully
        $this->assertContains(static::DATABASE_NAME, $databases);

        // assert index is created successfully
        $this->assertContains(static::FIELD_NAME, $index[CollectionHandler::OPTION_FIELDS]);
        $this->assertEquals(static::INDEX_NAME, $index[static::OPTION_NAME]);
        $this->assertEquals(CollectionHandler::OPTION_PERSISTENT_INDEX, $index[CollectionHandler::OPTION_TYPE]);
        $this->assertTrue($index[CollectionHandler::OPTION_UNIQUE]);
    }

    /**
     * Get all existing databases.
     */
    private function getDatabases(): array
    {
        $connection   = $this->getArangoConnection();
        $databases    = ArangoDatabase::databases($connection);

        return $databases['result'];
    }

    /**
     * Get index named @static::INDEX_NAME from collection @static::COLLECTION_NAME.
     */
    private function getIndex()
    {
        $connection        = $this->getArangoConnection(static::DATABASE_NAME);
        $collectionHandler = new CollectionHandler($connection);

        return $collectionHandler->getIndex(static::COLLECTION_NAME, static::INDEX_NAME);
    }

    /**
     * Return arango connection if not sent database name then
     * return _system database connection by default.
     */
    private function getArangoConnection(string $dbName = '_system'): ArangoConnection
    {
        return (new class {use ArangoConnectionTrait;})::getArangoConnection($dbName);
    }
}

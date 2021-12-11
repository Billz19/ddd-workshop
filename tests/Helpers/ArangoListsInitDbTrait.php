<?php


namespace Tests\Helpers;

use App\Packages\Lists\Repository\Arango\ListsArangoDbInitializer;

trait ArangoListsInitDbTrait
{
    /**
     * init database.
     */
    private function initDB(string $databaseName)
    {
        $initArangoDb = app()->make(ListsArangoDbInitializer::class);
        $initArangoDb->init($databaseName);
    }
}

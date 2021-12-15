<?php


namespace Tests\Helpers;

use App\Packages\Posts\Repository\Arango\PostArangoDbInitializer;

trait ArangoPostInitDbTrait
{
    /**
     * init database.
     */
    private function initDB(string $databaseName)
    {
        $initArangoDb = app()->make(PostArangoDbInitializer::class);
        $initArangoDb->init($databaseName);
    }
}

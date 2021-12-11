<?php


namespace Tests\Helpers;

use App\Packages\User\Repository\Arango\UserArangoDbInitializer;

trait ArangoUserInitDbTrait
{
    /**
     * init database.
     */
    private function initDB(string $databaseName)
    {
        $initArangoDb = app()->make(UserArangoDbInitializer::class);
        $initArangoDb->init($databaseName);
    }
}

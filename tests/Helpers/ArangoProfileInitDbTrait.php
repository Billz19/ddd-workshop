<?php


namespace Tests\Helpers;

use App\Packages\Profiles\Repository\Arango\ProfileArangoDbInitializer;
use App\Packages\Profiles\Repository\RepositoryInterface;
use Mockery;
use Mockery\MockInterface;

trait ArangoProfileInitDbTrait
{
    /**
     * init database.
     */
    private function initDB(string $databaseName)
    {
        $initArangoDb = app()->make(ProfileArangoDbInitializer::class);
        $initArangoDb->init($databaseName);
    }
}

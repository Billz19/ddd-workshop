<?php

namespace App\Providers;

use App\Packages\Users\Repository\UserRepositoryInterface;
use App\Packages\Users\UserService;
use App\Packages\Users\UserServiceInterface;
use App\Packages\Users\Repository\Arango\UserArangoDbInitializer;
use App\Packages\Users\Repository\Arango\UserArangoRepository;
use App\Packages\Users\Repository\Arango\UsersCollection;
use ArangoDBClient\Connection as ArangoConnection;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        // binding concrete repository to @UserRepositoryInterface
        $this->app->bind(
            UserRepositoryInterface::class,
            UserArangoRepository::class
        );

        // binding concrete class to @UserServiceInterface
        $this->app->bind(
            UserServiceInterface::class,
            UserService::class,
        );

        // binding instance to @UserArangoDbInitializer
        $this->app->bind(UserArangoDbInitializer::class, function() {
            return new UserArangoDbInitializer(
                app()->make(ArangoConnection::class),
                [
                    new UsersCollection,
                ]
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

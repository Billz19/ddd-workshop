<?php

namespace App\Providers;


use App\Packages\Posts\PostService;
use App\Packages\Posts\PostServiceInterface;
use App\Packages\Posts\Repository\Arango\PostArangoDbInitializer;
use App\Packages\Posts\Repository\Arango\PostArangoRepository;
use App\Packages\Posts\Repository\Arango\PostsCollection;
use App\Packages\Posts\Repository\PostRepositoryInterface;
use ArangoDBClient\Connection as ArangoConnection;
use Illuminate\Support\ServiceProvider;

class PostServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        // binding concrete repository to @PostRepositoryInterface
        $this->app->bind(
            PostRepositoryInterface::class,
            PostArangoRepository::class
        );

        // binding concrete class to @PostServiceInterface
        $this->app->bind(
            PostServiceInterface::class,
            PostService::class,
        );

        // binding instance to @PostArangoDbInitializer
        $this->app->bind(PostArangoDbInitializer::class, function() {
            return new PostArangoDbInitializer(
                app()->make(ArangoConnection::class),
                [
                    new PostsCollection,
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

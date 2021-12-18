<?php

namespace App\Providers;


use App\Library\ArangoDb\ArangoDbInitializerCollection;
use App\Packages\Posts\Repository\Arango\PostArangoDbInitializer;
use App\Packages\Users\Repository\Arango\UserArangoDbInitializer;
use ArangoDBClient\Connection as ArangoConnection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // binding connection to @ArangodbConnection
        $this->app->bind(ArangoConnection::class, function() {
            return new ArangoConnection(Config::get('database.connections.arangodb'));
        });

        //bind arangoDbInitializer classes to ArangoDbInitializerCollection
        $this->app->bind(ArangoDbInitializerCollection::class, function () {
            return new ArangoDbInitializerCollection(
                app()->make(UserArangoDbInitializer::class),
                app()->make(PostArangoDbInitializer::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

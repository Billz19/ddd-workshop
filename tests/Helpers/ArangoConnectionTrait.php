<?php


namespace Tests\Helpers;

use ArangoDBClient\ConnectionOptions as ArangoConnectionOptions;
use ArangoDBClient\Database as ArangoDatabase;
use ArangoDBClient\UpdatePolicy as ArangoUpdatePolicy;
use ArangoDBClient\Connection as ArangoConnection;

trait ArangoConnectionTrait
{
    /**
     * Return arango connection with new database created.
     */
    private function createDatabase(string $name = null): ArangoConnection
    {
        $databaseName = 'test_' . ($name ?? uniqid());
        $this->initDB($databaseName);
        return static::getArangoConnection($databaseName);
    }

    /**
     *
     * Clean test databases brfore and after s testing class.
     */
    public static function cleanArangoDatabases(): void
    {
        $connection = static::getArangoConnection();
        $databases  = ArangoDatabase::databases($connection);

        foreach($databases['result'] as $database) {
            if(strpos($database, 'test') === 0) {
                ArangoDatabase::delete($connection, $database);
            }
        }
    }

    /**
     * Bind repository with test database.
     */
    private function bindRepository(): void
    {
        $repository = app()->make(RepositoryInterface::class);
        if($repository instanceof Repository) {
            $conn = $this->createDatabase();
            app()->bind(
                RepositoryInterface::class,
                fn() => new Repository($conn)
            );
        }
    }

    /**
     * Return new @ArangoConnection with database sent in param.
     */
    public static function getArangoConnection(string $databaseName = '_system'): ArangoConnection
    {
        $connectionOptions = [
            // database name
            ArangoConnectionOptions::OPTION_DATABASE      => $databaseName,
            // server endpoint to connect to
            ArangoConnectionOptions::OPTION_ENDPOINT      => env('ARANGO_ENDPOINT','http://localhost:8630'),
            // authorization type to use (currently supported: 'Basic')
            ArangoConnectionOptions::OPTION_AUTH_TYPE     => env('ARANGO_AUTH_TYPE','Basic'),
            // user for basic authorization
            ArangoConnectionOptions::OPTION_AUTH_USER     => env('ARANGO_AUTH_USER','root'),
            // password for basic authorization
            ArangoConnectionOptions::OPTION_AUTH_PASSWD   => env('ARANGO_AUTH_PASSWD',''),
            // connection persistence on server. can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
            ArangoConnectionOptions::OPTION_CONNECTION    => env('ARANGO_CONNECTION','Keep-Alive'),
            // connect timeout in seconds
            ArangoConnectionOptions::OPTION_TIMEOUT       => env('ARANGO_TIMEOUT',3),
            // whether or not to reconnect when a keep-alive connection has timed out on server
            ArangoConnectionOptions::OPTION_RECONNECT     => env('ARANGO_RECONNECT',true),
            // optionally create new collections when inserting documents
            ArangoConnectionOptions::OPTION_CREATE        => env('ARANGO_CREATE',true),
            // optionally create new collections when inserting documents
            ArangoConnectionOptions::OPTION_UPDATE_POLICY => env('ARANGO_UPDATE_POLICY',ArangoUpdatePolicy::LAST),
        ];

        return new ArangoConnection($connectionOptions);
    }
}
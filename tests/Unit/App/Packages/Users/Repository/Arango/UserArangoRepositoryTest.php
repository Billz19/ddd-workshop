<?php

namespace Tests\Unit\App\Packages\Users\Repository\Arango;

use App\Packages\Exceptions\ResourceAlreadyExistsError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Exceptions\UnknownDBErrorException;
use App\Packages\Users\Repository\Arango\UserArangoRepository;
use ArangoDBClient\Connection as ArangoConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Tests\Data\Fixtures\UserFixture;
use Tests\Helpers\ArangoConnectionTrait;
use Tests\Helpers\ArangoUserInitDbTrait;
use Tests\TestCase;
use triagens\ArangoDb\CollectionHandler;

/**
 * @group Users
 */
class UserArangoRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use ArangoConnectionTrait;
    use ArangoUserInitDbTrait;

    public const ARANGO_DB_CONFIG = 'database.connections.arangodb';
    public const INVALID_ENDPOINT = 'http://localhost:85555';

    protected function setUp(): void
    {
        parent::setUp();
        // after the environment has been loaded we need to clear all test database before repository get initialized
        static::cleanArangoDatabases();
    }

    private function newRepositoryWithInvalidConnection()
    {
        $config = Config::get(static::ARANGO_DB_CONFIG);
        $config['endpoint'] = static::INVALID_ENDPOINT;

        return new UserArangoRepository(new ArangoConnection($config));
    }

    /**
     * @test
     */
    public function testCreateUser()
    {
        $conn = $this->createDatabase();
        $arangoRepository = new UserArangoRepository($conn);
        $colHandler = new CollectionHandler($conn);

        $user = UserFixture::newUser(withId: true);
        $result = $arangoRepository->createUser($user);

        $this->assertTrue($colHandler->has('users'));
        $this->assertEqualsCanonicalizing(
            $user->toArray(),
            $result->toArray()
        );
    }

    /**
     * @test
     */
    public function testCreateUserThrowsResourceAlreadyExistsError()
    {
        $this->expectException(ResourceAlreadyExistsError::class);
        $conn = $this->createDatabase();
        $arangoRepository = new UserArangoRepository($conn);

        $user = UserFixture::newUser(withId: true);
        $arangoRepository->createUser($user);
        $arangoRepository->createUser($user);
    }

    /**
     * @test
     */
    public function testCreateUserThrowsUnknownDBError()
    {
        $this->expectException(UnknownDBErrorException::class);
        $arangoRepository = $this->newRepositoryWithInvalidConnection();

        $user = UserFixture::newUser(withId: true);
        $arangoRepository->createUser($user);
    }

    /**
     * @test
     */
    public function testFindUserByEmail()
    {
        $conn = $this->createDatabase();
        $arangoRepository = new UserArangoRepository($conn);

        $user = UserFixture::newUser(withId: true);
        $arangoRepository->createUser($user);
        $result = $arangoRepository->findUserByEmail($user->getEmail());

        $this->assertEqualsCanonicalizing(
            Arr::except($user->toArray(), 'id'),
            Arr::except($result->toArray(), 'id')
        );
    }

    /**
     * @test
     */
    public function testFindUserByEmailThrowsResourceNotFoundError()
    {
        $this->expectException(ResourceNotFoundError::class);
        $conn = $this->createDatabase();
        $arangoRepository = new UserArangoRepository($conn);

        $arangoRepository->findUserByEmail('not_found_email');

    }

    /**
     * @test
     */
    public function testFindUserByEmailThrowsUnknownDBError()
    {
        $this->expectException(UnknownDBErrorException::class);
        $arangoRepository = $this->newRepositoryWithInvalidConnection();

        $arangoRepository->findUserByEmail('email');
    }
}

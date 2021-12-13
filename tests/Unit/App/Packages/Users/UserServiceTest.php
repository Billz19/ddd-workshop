<?php

namespace Tests\Unit\App\Packages\Users;

use App\Packages\Exceptions\ResourceAlreadyExistsError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Exceptions\UnknownDBErrorException;
use App\Packages\Users\Repository\Arango\UserArangoRepository;
use App\Packages\Users\UserService;
use Mockery;
use Mockery\MockInterface;
use Tests\Data\Fixtures\UserFixture;
use Tests\Helpers\ArangoConnectionTrait;
use Tests\TestCase;

/**
 * @group Users
 */
class UserServiceTest extends TestCase
{
    use ArangoConnectionTrait;

    private MockInterface $mockUsersRepository;
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        static::cleanArangoDatabases();

        $this->mockUsersRepository = Mockery::mock(UserArangoRepository::class);
        $this->userService = new UserService($this->mockUsersRepository);
    }

    /**
     * @test
     */
    public function testCreate()
    {
        $user = UserFixture::newUser();
        $this->mockUsersRepository
            ->shouldReceive('createUser')
            ->with($user)
            ->andReturn($user);

        $result = $this->userService->create($user);

        $this->assertEqualsCanonicalizing($result, $user);
    }

    /**
     * @test
     */
    public function testCreateThrowsResourceAlreadyExists()
    {
        $this->expectException(ResourceAlreadyExistsError::class);
        $user = UserFixture::newUser();
        $this->mockUsersRepository
            ->shouldReceive('createUser')
            ->with($user)
            ->andThrow(ResourceAlreadyExistsError::class);

        $this->userService->create($user);

    }

    /**
     * @test
     */
    public function testCreateThrowsUnknownDBError()
    {
        $this->expectException(UnknownDBErrorException::class);
        $user = UserFixture::newUser();
        $this->mockUsersRepository
            ->shouldReceive('createUser')
            ->with($user)
            ->andThrow(UnknownDBErrorException::class);

        $this->userService->create($user);
    }

    /**
     * @test
     */
    public function testFindUserByEmail()
    {
        $user = UserFixture::newUser();
        $this->mockUsersRepository
            ->shouldReceive('findUserByEmail')
            ->with($user->getEmail())
            ->andReturn($user);

        $result = $this->userService->findByEmail($user->getEmail());

        $this->assertEqualsCanonicalizing($result, $user);
    }

    /**
     * @test
     */
    public function testFindUserByEmailThrowsResourceNotFoundError()
    {
        $this->expectException(ResourceNotFoundError::class);
        $user = UserFixture::newUser();
        $this->mockUsersRepository
            ->shouldReceive('findUserByEmail')
            ->with($user->getEmail())
            ->andThrow(ResourceNotFoundError::class);

        $this->userService->findByEmail($user->getEmail());
    }

    /**
     * @test
     */
    public function testFindUserByEmailThrowsUnknownDBError()
    {
        $this->expectException(UnknownDBErrorException::class);
        $user = UserFixture::newUser();
        $this->mockUsersRepository
            ->shouldReceive('findUserByEmail')
            ->with($user->getEmail())
            ->andThrow(UnknownDBErrorException::class);

        $this->userService->findByEmail($user->getEmail());
    }
}

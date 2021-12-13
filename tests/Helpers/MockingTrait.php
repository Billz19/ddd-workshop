<?php


namespace Tests\Helpers;

use App\Packages\Users\Repository\Arango\UserArangoRepository;
use App\Packages\Users\Repository\UserRepositoryInterface;
use Mockery\MockInterface;

trait MockingTrait
{
    private static $WITH   = 'with';
    private static $RETURN = 'return';

    /**
     * Trigger the exception for the repository by mocking the repository methods
     * and binding mocked instance to the repository interface.
     */
    private function triggerRepositoryException(
        string $function,
        string $exception,
        string $RepositoryClassName = UserRepositoryInterface::class
    ): void {
        $this->instance(
            $RepositoryClassName,
            \Mockery::mock(
                $RepositoryClassName,
                fn (MockInterface $mock) => $mock->shouldReceive($function)->andThrows($exception)
            )
        );
    }

    /**
     * Mock the @RepositoryInterface methods by sending the hash map $receiveReturn where the key is the method name
     * and value is the return value, and bind mocked instance to @RepositoryInterface.
     * the method getList is mucked by default.
     */
    private function mockAndBindRepository(
        array $receiveReturn = [],
        string $repository = UserArangoRepository   ::class
    ): MockInterface {
        $mockListsRepository      = \Mockery::mock($repository);
        foreach ($receiveReturn as $method => $return) {
            if (is_array($return)) {
                $mockListsRepository
                    ->shouldReceive($method)
                    ->with(...$return[self::$WITH])
                    ->andReturn($return[self::$RETURN]);
            } else {
                $mockListsRepository->shouldReceive($method)->andReturn($return);
            }
        }

        return $this->instance(
            abstract: $repository,
            instance: $mockListsRepository
        );
    }
}

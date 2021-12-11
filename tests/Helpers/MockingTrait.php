<?php


namespace Tests\Helpers;

use App\Packages\Profiles\Repository\RepositoryInterface as ProfileRepositoryInterface ;
use App\Packages\Lists\Repository\RepositoryInterface as ListRepositoryInterface;
use Mockery\MockInterface;
use Tests\Data\ListFixture;

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
        string $RepositoryClassName = ProfileRepositoryInterface::class
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
     * build mock for the @NodesRepository and pass it to @ListRepository mock
     */
    private function mockNodesRepository(
        string $method,
        array $with = null,
        mixed $return = null,
        string $exception = null
    ): void {
        $mockNodesRepository = $this->getNodesRepository();
        $this->mockAndBindRepository([
            'getList'                   => ListFixture::newList(),
            'getNodesRepositoryForList' => $mockNodesRepository,
            'getNodesRepository'        => $mockNodesRepository
        ]);

        $mockNodesRepository = $mockNodesRepository->shouldReceive($method);

        if (!is_null($with)) {
            $mockNodesRepository->with(...$with);
        }

        if(is_null($return) && is_null($exception)) {
            return;
        }

        $methodName = is_null($return) ? 'andThrows': 'andReturn';
        $mockNodesRepository->$methodName($return ?? $exception);
    }

    /**
     * Mock the @RepositoryInterface methods by sending the hash map $receiveReturn where the key is the method name
     * and value is the return value, and bind mocked instance to @RepositoryInterface.
     * the method getList is mucked by default.
     */
    private function mockAndBindRepository(
        array $receiveReturn = [],
        string $repository = ListRepositoryInterface::class
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

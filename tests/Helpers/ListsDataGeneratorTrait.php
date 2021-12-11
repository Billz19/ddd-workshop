<?php


namespace Tests\Helpers;

use App\Packages\Lists\Models\ListManifest;
use App\Packages\Lists\Models\ListNode;
use App\Packages\Lists\Repository\Arango\NodesRepository;
use App\Packages\Lists\Repository\NodesRepositoryInterface;
use Mockery\MockInterface;
use Tests\Data\ListFixture;


trait ListsDataGeneratorTrait
{
    /**
     * Create new list and insert it in database.
     */
    private function insertTestList(string $name = 'Test List'): ListManifest
    {
        return ListFixture::newList($name);
    }

    /**
     * Create new list and insert it in database.
     */
    private function insertTestNode(): ListNode
    {

        return ListFixture::newListNode(withArn: true);
    }

    private function getNodesRepository($initialize = true): NodesRepositoryInterface|MockInterface
    {
        $nodeRepoMock = \Mockery::mock(NodesRepositoryInterface::class);
        if ($initialize){
            $nodeRepoMock->shouldReceive('init')->andReturn($nodeRepoMock);
        }
        return $nodeRepoMock;
    }
}

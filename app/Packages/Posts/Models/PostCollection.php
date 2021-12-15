<?php


namespace App\Packages\Posts\Models;


use App\Library\Collection\ModelCollection;
use App\Library\Collection\ModelCollectionSerializableTrait;

class PostCollection extends ModelCollection implements \JsonSerializable
{
    use ModelCollectionSerializableTrait;

    /**
     * @inheritDoc
     */
    protected function addItem(array $item): object
    {
        $returnItem   = Post::fromArray($item);
        $this->data[] = $returnItem;

        return $returnItem;
    }
}

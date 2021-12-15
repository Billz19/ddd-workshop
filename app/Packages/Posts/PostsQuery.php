<?php

namespace App\Packages\Posts;

use App\Library\Serialize\ArraySerializableTrait;

class PostsQuery extends Repository\PostsQuery implements \JsonSerializable
{
    use ArraySerializableTrait;
}

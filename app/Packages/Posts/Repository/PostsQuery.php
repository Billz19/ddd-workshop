<?php

namespace App\Packages\Posts\Repository;


use App\Library\Request\PaginationQuery;
use App\Library\Serialize\ArraySerializableTrait;

class PostsQuery implements \JsonSerializable
{

    use ArraySerializableTrait;
    use PaginationQuery;

}

<?php

namespace App\Library\Request;

trait IdsQuery {

    /**
     * @var array<string>
     */
    private array $ids = [];

    /**
     * return @IdsQuery $ids
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * set $ids to the @IdsQuery
     */
    public function setIds(array $ids): void
    {
        $this->ids = $ids;
    }
}

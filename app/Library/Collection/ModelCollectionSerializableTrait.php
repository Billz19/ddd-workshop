<?php

namespace App\Library\Collection;


trait ModelCollectionSerializableTrait
{
    /**
     * Transform models in $data to array and return it.
     */
    public function toArray(): array
    {
        return array_map(fn($d) => $d->toArray(), $this->data);
    }

    /**
     * Return associative array contains data of model
     * using to serialize model to json.
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}


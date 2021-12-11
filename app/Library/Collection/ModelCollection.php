<?php

namespace App\Library\Collection;

use App\Packages\Exceptions\InvalidArgumentError;
use Iterator;

/**
 * @ModelCollection is abstract class to represent collections for models.
 * and validate this type is we want.
 */
abstract class ModelCollection implements Iterator
{
    private $position;
    protected array $data = [];

    public function __construct(array $data) {
        $this->position = 0;
        foreach($data as $item) {
            $this->validateItem($item);
            $this->addItem($item);
        }
    }

    /**
     * Adds new item to the collection, this function must be defined in subclasses of @ModelCollection.
     * Implementation is subclasses are expected to check the type of the item and throw a
     * @InvalidArgumentError if the type is not supported.
     *
     * @throws InvalidArgumentError
     */
    abstract protected function addItem(array $item): object;

    private function initData(array $data): void
    {

    }
    /**
     * Validate the item data.
     *
     * @throws InvalidArgumentError
     */
    private function validateItem(array $item): void
    {
        if(! array_key_exists('id', $item)) {
            throw new InvalidArgumentError(message: "the array must contain at least the id");
        }
    }

    /**
     * Returns the $data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function rewind()  { $this->position = 0; }

    public function current() { return $this->data[$this->position]; }

    public function key()     { return $this->position; }

    public function next()    { ++$this->position; }

    public function valid()   { return isset($this->data[$this->position]); }
}


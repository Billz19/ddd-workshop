<?php


namespace App\Library\Serialize;

/**
 * @ArraySerializableInterface interface is array serializable provider
 * to use with models to serialize data
 */
interface ArraySerializableInterface
{
    /**
     * Return associative array contains data of model.
     */
    public function toArray(): array;

    /**
     * Transform array of data to profile model.
     */
    public static function fromArray(array $data): static;
}

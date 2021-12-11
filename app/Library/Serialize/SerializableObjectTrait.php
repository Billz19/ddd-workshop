<?php


namespace App\Library\Serialize;


trait SerializableObjectTrait
{
    private array $__properties;

    public function __construct()
    {
        $this->__properties = [];
    }

    public function __set($key, $value)
    {
        $this->__properties[$key] = $value;
    }

    public function __get($key)
    {
        $getterMethodName = 'get' . ucfirst($key);

        return method_exists($this, $getterMethodName)
            ? $this->$getterMethodName()
            : $this->__properties[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->getValues();
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): static
    {
        $object = new self();

        foreach ($data as $property => $value) {
            $setterMethodName = 'set' . ucfirst($property);
            if (method_exists($object, $setterMethodName)) {
                $object->$setterMethodName($value);
            } else {
                $object->$property = $value;
            }
        }
        return $object;
    }

    /**
     * Return array as a result of merging $__properties and array of private values if exists.
     */
    private function getValues(): array
    {
        return array_merge(
            $this->getInstancePrivateValues(),
            $this->__properties
        );
    }

    /**
     * Return array of private values.
     * overriding the method in the model when has private values.
     */
    private function getInstancePrivateValues(): array
    {
        return [];
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

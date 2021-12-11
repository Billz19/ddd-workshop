<?php


namespace App\Library\Serialize;

/**
 * Trait @ArraySerializableTrait implement logic for transform model to array and array to model.
 */
trait ArraySerializableTrait
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_map(function($property) {
            if(is_array($property)) {
                return array_map(
                    fn ($p) => $p instanceof ArraySerializableInterface ? $p->toArray() : $p,
                    $property
                );
            }
            elseif ($property instanceof ArraySerializableInterface) { return $property->toArray(); }
            return $property;
        },
            $this->getPrivateValues()
        );
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): static
    {
        $object = new self();

        foreach ($data as $property => $value) {
            static::setProperty($object, $property, $value);
        }

        return $object;
    }

    /**
     * Returns a key/value hash of private fields.
     */
    private function getPrivateValues(): array
    {
        $reflectionClass   = new \ReflectionClass($this);
        $objectVars        = get_object_vars($this);
        $privateProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE);

        if (empty($privateProperties) && method_exists($this,'__set')){
            return $objectVars;
        }
        $privateProperties = array_map(fn($p) => $p->getName(), $privateProperties);

        $privateValues = array_filter(
            $objectVars,
            fn($p) => in_array($p, $privateProperties),
            ARRAY_FILTER_USE_KEY
        );

        return array_filter(
            $privateValues,
            fn($p) => $p != 'metadata' || ($p == 'metadata' && !empty($this->$p)),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Assign value to property using setter if setter method exist
     * otherwise assign value to property directly.
     */
    private static function setProperty($object, string $property, mixed $value): void
    {
        $setterMethodName = 'set' . ucfirst($property);

        if(method_exists($object, $setterMethodName)) {
            $object->$setterMethodName($value);
        } elseif (method_exists($object, '__set')) {
            $object->$property = $value;
        }
    }

    /**
     * Transform array of data to array of $class objects.
     */
    private function arrayToArrayOfObjects(array $data, string $class): mixed
    {
        return array_map(
            fn ($d) => is_array($d) ? $class::fromArray($d) : $d,
            $data
        );
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

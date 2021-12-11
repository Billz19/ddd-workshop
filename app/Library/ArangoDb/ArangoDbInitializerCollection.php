<?php


namespace App\Library\ArangoDb;

/**
 * @ArangoDbInitializerCollection class is a data holder, which holds collection of @ArangoDbInitializer.
 */
class ArangoDbInitializerCollection
{
    /**
     * @var array<ArangoDbInitializer>
     */
    private array $initializers;

    public function __construct(
        ArangoDbInitializer ...$initializers,
    )
    {
        $this->initializers = $initializers;
    }

    public function getInitializers(): array
    {
        return $this->initializers;
    }
}

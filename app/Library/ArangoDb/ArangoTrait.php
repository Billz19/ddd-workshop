<?php


namespace App\Library\ArangoDb;


use ArangoDBClient\Cursor;
use ArangoDBClient\Statement as ArangoStatement;

/**
 * @ArangoTrait handle arango query execution.
 */
trait ArangoTrait
{
    /**
     * Execute query and return result as a @Cursor
     */
    private function executeQuery(string $query, array $bindVars): Cursor
    {
        $statement = new ArangoStatement(
            $this->connection,
            [
                'query' => $query,
                'bindVars' => $bindVars
            ]
        );

        return $statement->execute();
    }
}

<?php


namespace Tests\Helpers;


trait ErrorsJsonStructureTrait
{
    /**
     * Return errors json structure if debug is enabled then add debug in json structure.
     */
    private function getErrorsJsonStructure(): array
    {
        $structure = array('errors');
        if(env('APP_DEBUG', false)) {
            $structure['debug'] = ['stacktrace'];
        }

        return $structure;
    }

}

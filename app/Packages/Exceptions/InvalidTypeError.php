<?php

namespace App\Packages\Exceptions;

use Error;

class InvalidTypeError extends Error
{
    public function __construct(mixed $gotType, string ...$wantType)
    {
        $want = join('|', $wantType);
        $got  = is_object($gotType) ? $gotType::class : gettype($gotType);

        parent::__construct(message: "expected ${want} got ${got}");
    }
}

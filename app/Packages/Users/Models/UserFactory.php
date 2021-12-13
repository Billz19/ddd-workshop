<?php

namespace App\Packages\Users\Models;


class UserFactory{

    /**
     * Create new @User object from array of data.
     */
    public static function createUserFromArray(array $data): User
    {
        return User::fromArray($data);
    }
}

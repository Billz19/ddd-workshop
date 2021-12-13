<?php

namespace App\Packages\Users\Repository;

use App\Packages\Users\Models\User;

interface UserRepositoryInterface {

    public function createUser(User $user): User;
    public function findUserByEmail(string $email): User;
}

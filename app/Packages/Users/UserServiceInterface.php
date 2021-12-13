<?php

namespace App\Packages\Users;

use App\Packages\Users\Models\User;

interface UserServiceInterface {

    public function create(User $user): User;
    public function findByEmail(string $email): User;
}

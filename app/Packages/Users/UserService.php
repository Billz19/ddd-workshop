<?php


namespace App\Packages\Users;

use App\Packages\Users\Models\User;
use App\Packages\Users\Repository\Arango\UserArangoRepository;

/**
 * The default UserServiceInterface implementation.
 */
class UserService implements UserServiceInterface
{
    function __construct(
        private UserArangoRepository $userRepository,
    )
    {
    }

    public function create(User $user): User
    {
        return $this->userRepository->createUser($user);
    }

    public function findByEmail(string $email): User
    {
        return $this->userRepository->findUserByEmail($email);
    }

}

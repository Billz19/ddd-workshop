<?php

namespace Tests\Data\Fixtures;

use App\Packages\Users\Models\User;
use Faker\Factory;
use Faker\Provider\Person;

class UserFixture
{
    public static function newUser(bool $withPass = true, bool $withId = false): User
    {
        $userArray = [
            'name' => Person::titleMale(),
            'email' => Factory::create('en_GB')->email()
        ];

        if ($withPass) {
            $userArray['password'] = 'MySuperPassword84';
        }

        if ($withId) {
            $userArray['id'] = uniqid();
        }

        return User::fromArray($userArray);
    }
}

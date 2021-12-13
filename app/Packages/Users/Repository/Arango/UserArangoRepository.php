<?php


namespace App\Packages\Users\Repository\Arango;

use App\Library\ArangoDb\ArangoErrorCodes;
use App\Library\ArangoDb\ArangoTrait;
use App\Packages\Exceptions\ResourceAlreadyExistsError;
use App\Packages\Exceptions\ResourceNotFoundError;
use App\Packages\Exceptions\UnknownDBErrorException;
use App\Packages\Users\Models\User;
use App\Packages\Users\Repository\UserRepositoryInterface;
use ArangoDBClient\Connection as ArangoConnection;

class UserArangoRepository implements UserRepositoryInterface
{
    use ArangoTrait;

    public function __construct(
        private ArangoConnection $connection,
    )
    {
    }

    /**
     * @createUser inserts the @User $user into the database collection @static::USER_COLLECTION and
     * returns the created user ID.
     *
     * @throws @DbErrorException
     */
    public function createUser(User $user): User
    {
        $json = json_encode($user);

        try {
            $cursor = $this->executeQuery(
                query: "INSERT ${json} INTO @@users RETURN {result: MERGE({id: NEW._key},NEW)}",
                bindVars: [
                    '@users' => UsersCollection::COLLECTION,
                ]
            );
        } catch (\Exception $e) {
            if ($e->getCode() === ArangoErrorCodes::CONFLICT) {
                throw new ResourceAlreadyExistsError(message: "user with email \"{$user->getEmail()}\" already exists", previous: $e);
            } else {
                throw new UnknownDBErrorException(message: "could not create user", previous: $e);
            }
        }

        return User::fromArray($cursor->current()->get('result'));
    }

    /**
     * @findUserByEmail returns the @User identified by $email. It throws a @ResourceNotFoundError error
     * if the document does not exist.
     *
     * @throws ResourceNotFoundError
     * @throws UnknownDBErrorException
     */
    public function findUserByEmail(string $email): User
    {

        $query = "
               FOR user IN @@users
                 FILTER user.email == @email
                 RETURN {result : MERGE(user, {'id': user._key})}
            ";
        $bindVars = [
            '@users' => UsersCollection::COLLECTION,
            'email' => $email,
        ];
        try {
            $cursor = $this->executeQuery(
                query: $query,
                bindVars: $bindVars
            );
        } catch (\Exception $e) {
            throw new UnknownDBErrorException(message: "could not get user with email '$email'", previous: $e);
        }

        if ($cursor->getCount() === 0) {
            throw new ResourceNotFoundError("could not get user with email $email: user does not exists");
        }

        $result = $cursor->current()->get('result');
        return User::fromArray($result);
    }
}

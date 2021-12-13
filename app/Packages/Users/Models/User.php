<?php

namespace App\Packages\Users\Models;


use App\Library\Serialize\ArraySerializableInterface;
use App\Library\Serialize\ArraySerializableTrait;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use JsonSerializable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements ArraySerializableInterface, JsonSerializable
{
    use ArraySerializableTrait;
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use HasEvents;

    public $incrementing = false;
    protected $keyType = "string";

    private string $id;
    private string $email;
    private string $password;
    private string $name;


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
        $this->setAttribute('id', $id);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {

        $this->password = password_needs_rehash($password, PASSWORD_BCRYPT, ['cost' => 12]) ?
            password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) :
            $password;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

}

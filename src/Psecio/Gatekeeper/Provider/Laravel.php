<?php

namespace Psecio\Gatekeeper\Provider;

use Psecio\Gatekeeper\UserModel;
use Psecio\Gatekeeper\Gatekeeper;
use Illuminate\Auth\GenericUser;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserProviderInterface;
use Exception;

/**
 * This provider is for only Laravel 4.x based applications
 */
class Laravel implements UserProviderInterface
{
    /**
     * Init the object and use the Laravel DB config to
     *     set up the PDO object
     */
    public function __construct()
    {
        $config = \Config::get('database.connections.gatekeeper');
        Gatekeeper::init(null, array(
            'type' => $config['driver'],
            'username' => $config['username'],
            'password' => $config['password'],
            'host' => $config['host'],
            'name' => 'gatekeeper'
        ));
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param integer $identifier User ID
     *
     * @return \Illuminate\Auth\UserInterface
     */
    public function retrieveByID(int $identifier): UserInterface
    {
        $user = Gatekeeper::modelFactory('UserModel');
        $user->findById($identifier);
        return $this->returnUser($user);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     *
     * @return \Illuminate\Auth\UserInterface
     */
    public function retrieveByCredentials(array $credentials): UserInterface
    {
        $user = Gatekeeper::modelFactory('UserModel');
        $result = $user->findByUsername($credentials['username']);
        return $this->returnUser($user);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Auth\UserInterface  $user
     * @param  array $credentials
     *
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials): bool
    {
        return Gatekeeper::authenticate($credentials);
    }

    /**
     * Get the current "remember me" token value
     *
     * @param string $identifier Identifier for token location
     * @param string $token      [description]
     *
     * @throws \Exception Not implemented
     * @return \Exception
     */
    public function retrieveByToken(string $identifier, string $token): Exception
    {
        return new Exception('not implemented');
    }

    /**
     * Update the "remember me" token (not supported...yet)
     *
     * @param UserInterface $user User object
     * @param string $token Token string
     *
     * @throws \Exception Not implemented
     * @return \Exception
     */
    public function updateRememberToken(UserInterface $user, $token): Exception
    {
        return new Exception('not implemented');
    }

    /**
     * Reformat the Gatekeeper user into the Laravel user format
     *
     * @param \Psecio\Gatekeeper\UserModel $user User model instance
     * @return GenericUser instance
     */
    protected function returnUser(UserModel $user): GenericUser
    {
        $attrs = array(
            'id' => $user->id,
            'username' => $user->username,
            'password' => $user->password,
            'name' => $user->firstName . ' ' . $user->lastName,
            'email' => $user->email
        );
        return new GenericUser($attrs);
    }
}

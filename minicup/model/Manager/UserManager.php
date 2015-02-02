<?php

namespace Minicup\Model\Manager;


use LeanMapper\Exception\InvalidValueException;
use Minicup\Model\Entity\User;
use Minicup\Model\Repository\EntityNotFoundException;
use Minicup\Model\Repository\UserRepository;
use Nette\InvalidArgumentException;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;


class UserManager extends Object implements IAuthenticator
{

    /** @var UserRepository */
    private $UR;

    public function __construct(UserRepository $UR)
    {
        $this->UR = $UR;
    }

    /**
     * Performs an authentication.
     * @param   $credentials array
     * @return  Identity
     * @throws  AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;

        try {
            $user = $this->UR->findByUsername($username);
        } catch (EntityNotFoundException $e) {
            throw new AuthenticationException('Uživatel nenalezen.', self::IDENTITY_NOT_FOUND);
        }

        if (!Passwords::verify($password, $user->password_hash)) {
            throw new AuthenticationException('Zadaná kombinace není platná.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($user->password_hash)) {
            $user->password_hash = Passwords::hash($password);
            $this->UR->persist($user);
        }

        return new Identity($user->id, $user->role, array('fullname' => $user->fullname));
    }

    /**
     * Adds new user.
     * @param $username string
     * @param $password string
     * @param $fullname string
     * @param $role string
     * @throws InvalidValueException
     * @return int
     */
    public function add($username, $password, $fullname, $role = 'guest')
    {
        if ($this->UR->existsUsername($username)) {
            throw new InvalidArgumentException('Zadané uživatelské jméno již existuje.');
        }
        $user = new User;
        $user->username = $username;
        $user->password_hash = Passwords::hash($password);
        $user->role = $role;
        $user->fullname = $fullname;
        $id = $this->UR->persist($user);
        return $id;
    }
}


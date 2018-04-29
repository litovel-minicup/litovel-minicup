<?php

namespace Minicup\Model\Manager;


use Minicup\Model\Entity\User;
use Minicup\Model\Repository\UserRepository;
use Nette\InvalidArgumentException;

use Nette\SmartObject;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;


class UserManager implements IAuthenticator
{


    use SmartObject;

    /** @var UserRepository */
    private $UR;

    /**
     * @param UserRepository $UR
     */
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

        $user = $this->UR->findByUsername($username);

        if (!$user) {
            throw new AuthenticationException('Uživatel nenalezen.', self::IDENTITY_NOT_FOUND);
        }

        if (!Passwords::verify($password, $user->password_hash)) {
            throw new AuthenticationException('Zadaná kombinace není platná.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($user->password_hash)) {
            $user->password_hash = Passwords::hash($password);
            $this->UR->persist($user);
        }
        return new Identity($user->id, NULL, ['fullname' => $user->fullname]);

    }

    /**
     * Adds new user.
     * @param $username string
     * @param $password string
     * @param $fullname string
     * @return int
     */
    public function add($username, $password, $fullname)
    {
        if ($this->UR->existsUsername($username)) {
            throw new InvalidArgumentException('Zadané uživatelské jméno již existuje.');
        }
        $user = new User;
        $user->username = $username;
        $user->password_hash = Passwords::hash($password);
        $user->fullname = $fullname;
        $id = $this->UR->persist($user);
        return $id;
    }
}
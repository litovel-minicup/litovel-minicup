<?php

namespace Minicup\Model;

use Minicup\Model\Entity\User;
use Minicup\Model\Repository\UserRepository;
use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
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
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;

        try {
            $UE = $this->UR->findByUsername($username);
        } catch (\Exception $ex) {
            throw new Nette\Security\AuthenticationException('Uživatel nenalezen.', self::IDENTITY_NOT_FOUND);
        }

        if (!Passwords::verify($password, $UE->password_hash)) {
            throw new Nette\Security\AuthenticationException('Zadané heslo není platné.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($UE->password_hash)) {
            $UE->password_hash = Passwords::hash($password);
            $this->UR->persist($UE);
        }

        return new Nette\Security\Identity($UE->id, $UE->role, array('fullname' => $UE->fullname));
    }

    /**
     * Adds new user.
     * @param $username string
     * @param $password string
     * @param $fullname string
     * @param $role string
     * @throws \Exception
     * @return int
     */
    public function add($username, $password, $fullname, $role = 'guest')
    {
        if ($this->UR->existsUsername($username)) {
            throw new \Exception('Zadané uživatelské jméno již existuje.');
        }
        $UE = new User;
        $UE->username = $username;
        $UE->password_hash = Passwords::hash($password);
        $UE->role = $role;
        $UE->fullname = $fullname;
        $id = $this->UR->persist($UE);
        return $id;
    }

}

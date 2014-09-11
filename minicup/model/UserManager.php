<?php

namespace Minicup\Model;

use Nette,
    Nette\Security\Passwords,
    Minicup\Model\Entity\User;
        

/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator {

    /** @var \Minicup\Model\Repository\UserRepository */
    private $UR;

    public function __construct(\Minicup\Model\Repository\UserRepository $UR) {
        $this->UR = $UR;
    }

    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($username, $password) = $credentials;

        try {
            $UE = $this->UR->findByUsername($username);
        } catch (\Exception $ex) {
            throw new Nette\Security\AuthenticationException('User not found!', self::IDENTITY_NOT_FOUND);
        }

        if (!Passwords::verify($password, $UE->password_hash)) {
            throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($UE->password_hash)) {
            $UE->password_hash = Passwords::hash($password);
            $this->UR->persist($UE);
        }

        return new Nette\Security\Identity($UE->id, $UE->role, array('fullname' => $UE->fullname));
    }

    /**
     * Adds new user.
     * @param  string
     * @param  string
     * @return void
     */
    public function add($username, $password, $fullname, $role='guest') {
        if ($this->UR->existsUsername($username)) {
            throw new \Exception('Zadané uživatelské jméno již existuje.');
        }
        $UE = new User;
        $UE->username = $username;
        $UE->password_hash = Passwords::hash($password);
        $UE->role = $role;
        $UE->fullname = $fullname;
        $id = $this->UR->persist($UE);
        return new Nette\Security\Identity($id, $UE->role);
    }

}

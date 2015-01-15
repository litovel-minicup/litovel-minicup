<?php

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\User;

class UserRepository extends Repository
{
    /**
     * @param $username string
     * @return bool
     */
    public function existsUsername($username)
    {
        $user = $this->findByUsername($username);
        return $user ? TRUE : FALSE;
    }

    /**
     * @throws EntityNotFoundException
     * @param $username string
     * @return User
     */
    public function findByUsername($username)
    {
        $row = $this->createFluent()
            ->where('username = %s', $username)
            ->fetch();
        if ($row === false) {
            throw new EntityNotFoundException('User not found!');
        }
        return $this->createEntity($row);
    }
}

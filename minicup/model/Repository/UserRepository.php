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
        try {
            $this->findByUsername($username);
        } catch (\Exception $ex) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @throws \Exception
     * @return User
     */
    public function findByUsername($username)
    {
        $row = $this->connection->select('*')
            ->from($this->getTable())
            ->where('username = %s', $username)
            ->fetch();
        if ($row === false) {
            throw new \Exception('This username was not found.');
        }
        return $this->createEntity($row);
    }
}

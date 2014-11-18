<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Minicup\Model\Repository;

use Minicup\Model\Entity\User;

class UserRepository extends Repository {
    /**
     * @throws \Exception
     * @return User
     */
    public function findByUsername($username) {
        $row = $this->connection->select('*')
                ->from($this->getTable())
                ->where('username = %s', $username)
                ->fetch();
        if ($row === false) {
            throw new \Exception('This username was not found.');
        }
        return $this->createEntity($row);
    }
    
    /**
     * @param $username string
     * @return bool
     */
    public function existsUsername($username) {
        try {
            $e = $this->findByUsername($username);
        } catch (\Exception $ex) {
            return FALSE;
        }
        return TRUE;
    }
}

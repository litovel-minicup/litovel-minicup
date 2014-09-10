<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Minicup\Model\Repository;

class UserRepository extends Repository {
    /**
     * @return \Minicup\Model\Entity\User
     */
    public function findByUsername($username) {
        $row = $this->connection->select('*')
                ->from($this->getTable())
                ->where('username = %i', $username)
                ->fetch();
        if ($row === false) {
            throw new \Exception('Entity was not found.');
        }
        return $this->createEntity($row);
    }

}

<?php

namespace Minicup\Model\Entity;

/**
 * @property int        $id
 * @property string     $username       user's nick
 * @property string     $fullname       fullname
 * @property string     $password_hash  password hash
 * @property string     $role           user role
 *
 */
class User extends BaseEntity
{
    public $CACHE_TAG = 'user';
}

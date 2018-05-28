<?php

namespace Minicup\Model\Entity;

/**
 * @property int    $id
 * @property string $username       user's nick
 * @property string $fullname       fullname
 * @property string $password_hash  password hash
 * @property string $pin  PIN
 *
 */
class User extends BaseEntity
{
    public static $CACHE_TAG = 'user';
}

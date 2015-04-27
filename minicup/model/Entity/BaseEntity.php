<?php

namespace Minicup\Model\Entity;


use LeanMapper\Entity;

abstract class BaseEntity extends Entity
{
    public static $CACHE_TAG = 'entity';
}
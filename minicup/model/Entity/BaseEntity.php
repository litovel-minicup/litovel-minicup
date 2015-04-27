<?php

namespace Minicup\Model\Entity;


use LeanMapper\Entity;

abstract class BaseEntity extends Entity
{
    public $CACHE_TAG = 'entity';
}
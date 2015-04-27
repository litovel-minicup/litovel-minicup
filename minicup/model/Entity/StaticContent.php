<?php

namespace Minicup\Model\Entity;

/**
 * @property int            $id
 * @property string         $slug
 * @property string         $content
 * @property \DibiDateTime  $updated
 */
class StaticContent extends BaseEntity
{
    public $CACHE_TAG = 'staticContent';
}

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
    public static $CACHE_TAG = 'staticContent';
}

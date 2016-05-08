<?php

namespace Minicup\Model\Entity;
use Dibi\DateTime;

/**
 * @property int      $id
 * @property string   $slug
 * @property string   $content
 * @property DateTime $updated
 * @property Year $year m:hasOne
 */
class StaticContent extends BaseEntity
{
    public static $CACHE_TAG = 'staticContent';

    protected function initDefaults()
    {
        parent::initDefaults();
        $this->updated = new DateTime();
    }


}

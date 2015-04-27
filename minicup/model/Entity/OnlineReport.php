<?php

namespace Minicup\Model\Entity;

/**
 * @property int       $id
 * @property Match     $match m:hasOne  for which match is this online report
 * @property string    $type            type of this report
 * @property string    $message         message of this report
 * @property \Datetime $added           datetime when is added
 * @property int       $updated         datetime when is last updated
 *
 */
class OnlineReport extends BaseEntity
{
    public static $CACHE_TAG = 'onlineReport';
}

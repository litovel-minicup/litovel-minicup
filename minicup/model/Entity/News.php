<?php

namespace Minicup\Model\Entity;


/**
 * @property int       $id
 * @property string    $title
 * @property string    $content
 * @property \DateTime $updated
 * @property \Datetime $added
 * @property Year      $year m:hasOne
 * @property int       $texy
 */
class News extends BaseEntity
{
    public static $CACHE_TAG = 'news';

}
<?php

namespace Minicup\Model\Entity;


/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property \DateTime $updated
 * @property \Datetime $added
 */
class News extends BaseEntity
{
    public $CACHE_TAG = 'news';

}
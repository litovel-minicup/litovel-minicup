<?php

namespace Minicup\Model\Entity;

/**
 * @property int         $id
 * @property int         $year                       int for year
 * @property string|NULL $name                       optional name
 * @property string      $slug                       unique slug
 * @property int         $actual                     flag for actual year
 * @property Day[]       $days m:belongsToMany       game days
 * @property Category[]  $categories m:belongsToMany year categories
 * @property News[]      $news m:belongsToMany year news
 * @property Tag[]       $tags m:belongsToMany year photo tags
 */
class Year extends BaseEntity
{
    public static $CACHE_TAG = 'year';
} 
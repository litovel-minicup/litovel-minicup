<?php

namespace Minicup\Model\Entity;

/**
 * @property int           $id
 * @property Photo[]       $photos m:hasMany(:photo_tag) m:filter(activePhotos)    photos for this tag
 * @property string|NULL   $name name for tag
 * @property string        $slug unique slug
 * @property int           $isMain = 0 flag for gallery
 * @property Photo|NULL    $mainPhoto m:hasOne(main_photo_id) main photo for tag
 * @property TeamInfo|NULL $teamInfo m:belongsToOne
 * @property News|NULL     $news m:belongsToOne
 * @property Year|NULL     $year m:hasOne(year_id)
 *
 * @method addToPhotos(Photo $photo)
 * @method removeFromPhotos
 * @method removeAllPhotos
 * @method removeAllNews
 */
class Tag extends BaseEntity
{
    public static $CACHE_TAG = 'tag';
}
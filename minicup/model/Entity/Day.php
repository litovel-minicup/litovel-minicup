<?php
namespace Minicup\Model\Entity;


/**
 * @property int         $id
 * @property \DateTime   $day                           datetime of this day
 * @property Year        $year m:hasOne                 year for this day
 * @property MatchTerm[] $matchTerms m:belongsToMany    match terms in this day
 */
class Day extends BaseEntity
{
    public $CACHE_TAG = 'day';
}
<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int            $id
 * @property int            $order = 0          order of team in table
 * @property int            $points             points from actually played matches
 * @property int            $scored             sum of scored goals
 * @property int            $received           sum of received goals
 * @property Category       $category m:hasOne  category where is team in
 * @property Match[]        $matches            all matches for team
 * @property int            $actual = 1         flag for actual state
 * @property Match|NULL     $afterMatch m:hasOne(after_match_id)    after this match is this team inserted
 * @property \DateTime|NULL $inserted           datetime of inserted
 * @property TeamInfo       $i m:hasOne         main team info
 */
class Team extends Entity
{
    /**
     * for abbr request for slug, name or matches
     * @param string $name
     * @return mixed|string
     * @inheritdoc
     */
    public function __get($name /*, array $filterArgs*/)
    {
        if (in_array($name, array('slug', 'name', 'matches'))) {
            return $this->i->$name;
        }
        return parent::__get($name);
    }
}

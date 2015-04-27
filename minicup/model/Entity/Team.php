<?php

namespace Minicup\Model\Entity;

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
 * @property-read Match[]   $wins               win matches
 * @property-read Match[]   $draws              draw matches
 * @property-read Match[]   $loses              lose matches
 */
class Team extends BaseEntity
{
    public static $CACHE_TAG = 'team';

    /** @var Match[] */
    private $wins = array();

    /** @var Match[] */
    private $draws = array();

    /** @var Match[] */
    private $loses = array();

    /**
     * for abbr request for slug, name or matches
     * @param string $name
     * @return mixed|string
     * @inheritdoc
     */
    public function __get($name /*, array $filterArgs*/)
    {
        if (in_array($name, array('slug', 'name', 'matches', 'staticContent'))) {
            return $this->i->$name;
        }
        return parent::__get($name);
    }

    /**
     * @return Match[]
     */
    public function getPlayedMatches()
    {
        return array_filter($this->matches, function (Match $match) {
            return (bool)$match->confirmed;
        });
    }

    /**
     * @return Match[]
     */
    public function getWins()
    {
        if (!$this->wins) {
            foreach ($this->matches as $match) {
                if ($match->isWinner($this)) {
                    $this->wins[] = $match;
                }
            }
        }
        return $this->wins;
    }

    /**
     * @return Match[]
     */
    public function getLoses()
    {
        if (!$this->loses) {
            foreach ($this->matches as $match) {
                if ($match->isLoser($this)) {
                    $this->loses[] = $match;
                }
            }
        }
        return $this->loses;
    }

    /**
     * @return Match[]
     */
    public function getDraws()
    {
        if (!$this->draws) {
            foreach ($this->matches as $match) {
                if ($match->isDraw()) {
                    $this->draws[] = $match;
                }
            }
        }
        return $this->draws;
    }
}

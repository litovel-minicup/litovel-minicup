<?php

namespace Minicup\Model\Entity;

use LeanMapper\Entity;

/**
 * @property int            $id
 * @property Category       $category m:hasOne                              category
 * @property TeamInfo       $homeTeam m:hasOne(home_team_info_id:team_info) home team
 * @property TeamInfo       $awayTeam m:hasOne(away_team_info_id:team_info) away team
 * @property int|NULL       $scoreHome                                      score of home team
 * @property int|NULL       $scoreAway                                      score of away team
 * @property int            $confirmed                                      flag if is it really confirmed
 * @property MatchTerm      $matchTerm m:hasOne(match_term_id:match_term)   term fo this match
 * @property OnlineReport[] $onlineReports m:belongsToMany(:online_report)  reports
 */
class Match extends Entity
{
    /**
     * @return int|string
     */
    public function getScoreHome()
    {
        return $this->__get('scoreHome') ? $this->__get('scoreHome') : ' - ';
    }

    /**
     * @return int|string
     */
    public function getScoreAway()
    {
        return $this->__get('scoreAway') ? $this->__get('scoreAway') : ' - ';
    }

    /**
     * @param Team $team
     * @return bool
     */
    public function isWinner(Team $team)
    {
        if (!$this->confirmed) {
            return FALSE;
        }
        return
            ($team->i->id == $this->homeTeam->id && $this->scoreHome > $this->scoreAway) ||
            ($team->i->id == $this->awayTeam->id && $this->scoreAway > $this->scoreHome);
    }

    /**
     * @param Team $team
     * @return bool
     */
    public function isLoser(Team $team)
    {
        if (!$this->confirmed) {
            return FALSE;
        }
        return
            ($team->i->id == $this->homeTeam->id && $this->scoreHome < $this->scoreAway) ||
            ($team->i->id == $this->awayTeam->id && $this->scoreAway < $this->scoreHome);
    }

    /**
     * @return bool
     */
    public function isDraw()
    {
        if (!$this->confirmed) {
            return FALSE;
        }
        return $this->scoreHome == $this->scoreAway;
    }
}

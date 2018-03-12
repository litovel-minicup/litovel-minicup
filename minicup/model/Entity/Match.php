<?php

namespace Minicup\Model\Entity;

use Dibi\DateTime;
use Nette\InvalidArgumentException;

/**
 * @property int            $id
 * @property Category       $category                                       m:hasOne                              category
 * @property TeamInfo       $homeTeam                                       m:hasOne(home_team_info_id:team_info) home team
 * @property TeamInfo       $awayTeam                                       m:hasOne(away_team_info_id:team_info) away team
 * @property int|NULL       $scoreHome                                      score of home team
 * @property int|NULL       $scoreAway                                      score of away team
 * @property DateTime|NULL  $confirmed                                      datetime of confirming or NULL if unconfirmed
 * @property int|NULL       $confirmedAs                                    order of confirming in category or NULL if unconfirmed
 * @property MatchTerm      $matchTerm                                      m:hasOne(match_term_id:match_term)   term for this match
 * @property Team[]         $historyTeams                                   m:belongsToMany(after_match_id)   history teams
 */
class Match extends BaseEntity
{
    public static $CACHE_TAG = 'match';

    /**
     * @return int|string
     */
    public function getScoreHome()
    {
        return !is_null($score = $this->get('scoreHome')) ? $score : ' - ';
    }

    /**
     * @return int|string
     */
    public function getScoreAway()
    {
        return !is_null($score = $this->get('scoreAway')) ? $score : ' - ';
    }

    /**
     * @param TeamInfo|Team $teamInfo
     * @return bool
     */
    public function isWinner($teamInfo)
    {
        if ($teamInfo instanceof Team) {
            $teamInfo = $teamInfo->i;
        } elseif (!$teamInfo instanceof TeamInfo) {
            throw new InvalidArgumentException('Unknown given argument');
        }
        if (!$this->confirmed) {
            return FALSE;
        }
        return
            ($teamInfo->id === $this->homeTeam->id && $this->scoreHome > $this->scoreAway) ||
            ($teamInfo->id === $this->awayTeam->id && $this->scoreAway > $this->scoreHome);
    }

    /**
     * @param Team|TeamInfo $teamInfo
     * @return bool
     */
    public function isLoser($teamInfo)
    {
        if ($teamInfo instanceof Team) {
            $teamInfo = $teamInfo->i;
        } elseif (!$teamInfo instanceof TeamInfo) {
            throw new InvalidArgumentException('Unknown given argument');
        }
        if (!$this->confirmed) {
            return FALSE;
        }
        return
            ($teamInfo->id === $this->homeTeam->id && $this->scoreHome < $this->scoreAway) ||
            ($teamInfo->id === $this->awayTeam->id && $this->scoreAway < $this->scoreHome);
    }

    /**
     * @return bool
     */
    public function isDraw()
    {
        if (!$this->confirmed) {
            return FALSE;
        }
        return $this->scoreHome === $this->scoreAway;
    }

    /**
     * @param TeamInfo $teamInfo
     * @return TeamInfo
     */
    public function getRival(TeamInfo $teamInfo)
    {
        return $this->homeTeam->id === $teamInfo->id ? $this->awayTeam : $this->homeTeam;
    }


}

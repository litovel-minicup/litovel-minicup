<?php

namespace Minicup\Model\Entity;

use Nette\InvalidArgumentException;

/**
 * @property int            $id
 * @property Category       $category        m:hasOne                              category
 * @property TeamInfo       $homeTeam        m:hasOne(home_team_info_id:team_info) home team
 * @property TeamInfo       $awayTeam        m:hasOne(away_team_info_id:team_info) away team
 * @property int|NULL       $scoreHome       score of home team
 * @property int|NULL       $scoreAway       score of away team
 * @property \DateTime|NULL $confirmed       datetime of confirming or NULL if unconfirmed
 * @property int|NULL       $confirmedAs     order of confirming in category or NULL if unconfirmed
 * @property MatchTerm      $matchTerm       m:hasOne(match_term_id:match_term)   term for this match
 * @property Team[]         $historyTeams    m:belongsToMany(after_match_id)   history teams
 * @property MatchEvent[]   $events          m:belongsToMany all game events
 *
 * @property string         $onlineState
 * @property \DateTime|NULL $firstHalfStart  real time of match started
 * @property \DateTime|NULL $secondHalfStart real time of second halt start
 */
class Match extends BaseEntity
{
    const HALF_LENGTH = "P600S";

    public static $CACHE_TAG = 'match';

    /**
     * @return int|string
     * @throws \LeanMapper\Exception\InvalidStateException
     * @throws \LeanMapper\Exception\InvalidValueException
     * @throws \LeanMapper\Exception\MemberAccessException
     */
    public function getScoreHome()
    {
        return !is_null($score = $this->get('scoreHome')) ? $score : ' - ';
    }

    /**
     * @return int|string
     * @throws \LeanMapper\Exception\InvalidStateException
     * @throws \LeanMapper\Exception\InvalidValueException
     * @throws \LeanMapper\Exception\MemberAccessException
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

    /**
     * Returns index of half, counted from 0.
     * @return int|NULL
     */
    public function getHalfIndex()
    {
        $index = !is_null($this->firstHalfStart) + !is_null($this->secondHalfStart) - 1;
        return $index >= 0 ? $index : NULL;
    }

    /**
     * Gets name of online state.
     * @return string
     */
    public function getOnlineStateName()
    {
        //dump($this->onlineState);
        return [
            'init' => 'před zápasem',
            'half_first' => '1. poločas',
            'pause' => 'přestávka',
            'half_second' => '2. poločas',
            'end' => 'po zápase'
        ][$this->onlineState ?: 'init'];
    }
}

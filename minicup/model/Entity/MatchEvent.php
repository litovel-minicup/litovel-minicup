<?php

namespace Minicup\Model\Entity;


/**
 * @property int           $id
 * @property Match         $match m:hasOne
 * @property int|NULL      $scoreHome actual score of home team
 * @property int|NULL      $scoreAway actual score of away team
 * @property string|NULL   $message message of this event
 * @property string        $type m:enum(self::TYPE_*)
 * @property int           $halfIndex m:enum(self::HALF_INDEX_*)
 * @property int           $timeOffset total seconds from start of current half of match
 * @property Player|NULL   $player m:hasOne optionally linked to player
 * @property TeamInfo|NULL $teamInfo m:hasOne optionally linked to player
 */
class MatchEvent extends BaseEntity
{
    public static $CACHE_TAG = 'game_event';

    const TYPE_START = 'start';
    const TYPE_GOAL = 'goal';
    const TYPE_END = 'end';
    const TYPE_INFO = 'info';

    const HALF_INDEX_FIRST = 0;
    const HALF_INDEX_SECOND = 1;

    /**
     * Returns absolute time of match event.
     * @return \Dibi\DateTime|NULL
     * @throws \Exception
     */
    public function getAbsoluteTime()
    {
        switch ($this->halfIndex) {
            case self::HALF_INDEX_FIRST:
                $copied = clone $this->match->firstHalfStart;
                break;
            case self::HALF_INDEX_SECOND:
                $copied = clone $this->match->secondHalfStart;
                break;
            default:
                throw new \InvalidArgumentException('Unknown half index.');
        }
        $copied->add(new \DateInterval("PT{$this->timeOffset}S"));
        return $copied;
    }

    public function serialize()
    {
        $teamIndex = array_search(
            $this->teamInfo ? $this->teamInfo->id : NULL,
            [$this->match->homeTeam->id, $this->match->awayTeam->id],
            TRUE
        );
        return [
            'id' => $this->id,
            'score' => [$this->scoreHome, $this->scoreAway],
            'message' => $this->message,
            'type' => $this->type,
            'time_offset' => $this->timeOffset,
            'half_index' => $this->halfIndex,
            'team_index' => $teamIndex === FALSE ? -1 : $teamIndex,
            'player_name' => $this->player ? "{$this->player->name} {$this->player->surname}" : NULL,
            'player_number' => $this->player ? $this->player->number : NULL,
        ];
    }
}
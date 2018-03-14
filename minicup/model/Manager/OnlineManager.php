<?php

namespace Minicup\Model\Manager;


use InvalidArgumentException;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\MatchEvent;
use Minicup\Model\Entity\Player;
use Minicup\Model\Repository\MatchEventRepository;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\PlayerRepository;
use Nette\SmartObject;

class OnlineManager
{
    use SmartObject;

    /** @var PlayerRepository */
    private $PR;

    /** @var MatchEventRepository */
    private $MER;

    /** @var MatchRepository */
    private $MR;

    /**
     * OnlineManager constructor.
     * @param PlayerRepository     $PR
     * @param MatchEventRepository $MER
     * @param MatchRepository      $MR
     */
    public function __construct(PlayerRepository $PR, MatchEventRepository $MER, MatchRepository $MR)
    {
        $this->PR = $PR;
        $this->MER = $MER;
        $this->MR = $MR;
    }

    /**
     * @param Match  $match
     * @param Player $player
     * @return MatchEvent
     * @throws \LeanMapper\Exception\InvalidArgumentException
     */
    public function saveGoal(Match $match, Player $player)
    {
        $goal = new MatchEvent();

        $playerTeam = $player->teamInfo;

        if ($playerTeam->id == $match->homeTeam->id) {
            $match->scoreHome++;
        } else if ($playerTeam->id == $match->awayTeam->id) {
            $match->scoreAway++;
        } else {
            throw new InvalidArgumentException('Player cannot play in this match.');
        }

        $goal->scoreHome = $match->scoreHome;
        $goal->scoreAway = $match->scoreAway;
        // first -> 0, second -> 1
        $goal->halfIndex = $match->getHalfIndex();
        $goal->type = MatchEvent::TYPE_GOAL;
        $goal->player = $player;
        $goal->match = $match;

        if ($match->secondHalfStart) {
            $goal->halfIndex = MatchEvent::HALF_INDEX_SECOND;
        } elseif ($match->firstHalfStart) {
            $goal->halfIndex = MatchEvent::HALF_INDEX_FIRST;
        } else {
            throw new InvalidArgumentException('Not started match');
        }

        $reference = $goal->halfIndex == MatchEvent::HALF_INDEX_SECOND ? $match->secondHalfStart : $match->firstHalfStart;
        $goal->timeOffset = (new \DateTimeImmutable())->getTimestamp() - $reference->getTimestamp();

        $goal->message = "Player {$player->name} {$player->surname} from {$playerTeam->name} scoring to {$match->scoreHome}:{$match->scoreAway}.";

        $this->MER->persist($goal);
        $this->MR->persist($match);

        return $goal;
    }

    public function startHalf(Match $match)
    {
        if ($match->firstHalfStart == NULL) {
            $match->firstHalfStart = new \DateTime();
        } elseif ($match->secondHalfStart == NULL) {
            $match->secondHalfStart = new \DateTime();
        } else {
            throw new InvalidArgumentException("Match has already started.");
        }

        $this->MR->persist($match);
    }

}
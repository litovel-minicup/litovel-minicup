<?php

namespace Minicup\Model\Manager;


use Dibi\DateTime;
use Dibi\Exception;
use LeanMapper\Connection;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Team;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\MatchTermRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\InvalidArgumentException;
use Nette\Object;

class MatchManager extends Object
{
    /** @var MatchRepository */
    private $MR;

    /** @var TeamDataRefresher */
    private $TDR;

    /** @var TeamReplicator */
    private $replicator;

    /** @var ReorderManager */
    private $RM;

    /** @var Connection */
    private $connection;

    /** @var MatchTermRepository */
    private $MTR;

    /** @var TeamRepository */
    private $TR;

    /**
     * @param MatchRepository     $MR
     * @param TeamDataRefresher   $TDR
     * @param TeamReplicator      $replicator
     * @param ReorderManager      $RM
     * @param Connection          $connection
     * @param MatchTermRepository $MTR
     * @param TeamRepository      $TR
     */
    public function __construct(MatchRepository $MR,
                                TeamDataRefresher $TDR,
                                TeamReplicator $replicator,
                                ReorderManager $RM,
                                Connection $connection,
                                MatchTermRepository $MTR,
                                TeamRepository $TR)
    {
        $this->MR = $MR;
        $this->TDR = $TDR;
        $this->replicator = $replicator;
        $this->RM = $RM;
        $this->connection = $connection;
        $this->MTR = $MTR;
        $this->TR = $TR;
    }

    /**
     * Finds all matches confirmed later the current, for this matches deletes all history teams.
     * After that regenerates for repaired match.
     *
     * @param Match $match repaired match
     * @throws Exception
     * @throws \Exception
     */
    public function regenerateFromMatch(Match $match)
    {
        if ($match->confirmed === NULL) {
            throw new InvalidArgumentException('Invalid given match, must be confirmed');
        }
        $this->MR->persist($match);
        $this->connection->begin();
        try {
            /** @var Match[] $matchesAfter */
            $matchesAfter = $this->MR->findMatchesConfirmedAfterMatch($match);
            /** @var Match $match */
            foreach ($matchesAfter as $_match) {
                /** @var Team $historyTeam */
                foreach ($_match->historyTeams as $historyTeam) {
                    $this->TR->delete($historyTeam);
                }
            }
            $matchBefore = $this->MR->getMatchConfirmedBeforeMatch(reset($matchesAfter));
            if ($matchBefore) {
                $actualTeams = $matchBefore->historyTeams;
            } else {
                $actualTeams = $this->TR->findInitTeams($match->category);
            }
            foreach ($actualTeams as $actualTeam) {
                $actualTeam->actual = 1;
                $this->TR->persist($actualTeam);
            }
            foreach ($matchesAfter as $_match) {
                $this->confirmMatch($_match, $_match->scoreHome, $_match->scoreAway);
            }
        } catch (\Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
        $this->connection->commit();

    }

    /**
     * Set scores to match, replicate history table, refresh points in actual teams and reorder teams.
     * Whole in transaction.
     *
     * @param Match    $match
     * @param          $scoreHome
     * @param          $scoreAway
     * @throws Exception
     * @throws \Exception
     */
    public function confirmMatch(Match $match, $scoreHome, $scoreAway)
    {
        $category = $match->category;
        $this->connection->begin();
        try {
            $match->scoreHome = $scoreHome;
            $match->scoreAway = $scoreAway;
            $match->confirmed = new DateTime();
            $this->MR->persist($match);
            $this->replicator->replicate($category, $match);
            $this->TDR->refreshData($category);
            $this->RM->reorder($category);
        } catch (\Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
        $this->connection->commit();
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function isPlayingTime(Category $category)
    {
        $now = new DateTime();
        return NULL !== $this->MTR->getInTime($now);
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function isStarted(Category $category)
    {
        foreach ($category->matches as $match) {
            if ($match->confirmed) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function isFinished(Category $category)
    {
        foreach ($category->matches as $match) {
            if (!$match->confirmed) {
                return FALSE;
            }
        }
        return TRUE;
    }
}
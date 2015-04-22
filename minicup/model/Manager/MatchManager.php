<?php

namespace Minicup\Model\Manager;


use LeanMapper\Connection;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\MatchTermRepository;
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

    public function __construct(MatchRepository $MR, TeamDataRefresher $TDR, TeamReplicator $replicator, ReorderManager $RM, Connection $connection, MatchTermRepository $MTR)
    {
        $this->MR = $MR;
        $this->TDR = $TDR;
        $this->replicator = $replicator;
        $this->RM = $RM;
        $this->connection = $connection;
        $this->MTR = $MTR;
    }

    /**
     * @param Match $match
     * @param Category $category
     * @param $scoreHome
     * @param $scoreAway
     * @throws \DibiException
     * @throws \Exception
     */
    public function confirmMatch(Match $match, Category $category, $scoreHome, $scoreAway)
    {
        $this->connection->begin();
        try {
            $match->scoreHome = $scoreHome;
            $match->scoreAway = $scoreAway;
            $match->confirmed = 1;
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
        $now = new \DibiDateTime();
        return (bool) $this->MTR->getInTime($now);
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
}
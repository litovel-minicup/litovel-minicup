<?php

namespace Minicup\Model\Manager;


use Minicup\Model\Entity\Match;
use Minicup\Model\Repository\MatchRepository;
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

    function __construct(MatchRepository $MR, TeamDataRefresher $TDR, TeamReplicator $replicator, ReorderManager $RM)
    {
        $this->MR = $MR;
        $this->TDR = $TDR;
        $this->replicator = $replicator;
        $this->RM = $RM;
    }


    /**
     * @param Match $match
     * @param $scoreHome
     * @param $scoreAway
     * @return Match
     */
    public function confirmMatch(Match $match, $scoreHome, $scoreAway)
    {
        $category = $match->category;
        $match->scoreHome = $scoreHome;
        $match->scoreAway = $scoreAway;
        $match->confirmed = 1;
        $this->MR->persist($match);
        $this->replicator->replicate($category, $match);
        $this->TDR->refreshData($category);
        $this->RM->reorder($category);
    }
}
<?php

namespace Minicup\Model\Manager;


use Minicup\Model\Entity\Match;
use Minicup\Model\Repository\MatchRepository;
use Nette\Object;

class MatchManager extends Object
{
    /** @var MatchRepository */
    private $MR;

    /**
     * @param Match $match
     * @param $scoreHome
     * @param $scoreAway
     * @return Match
     */
    public function confirmMatch(Match $match, $scoreHome, $scoreAway)
    {
        return $match;
    }
}
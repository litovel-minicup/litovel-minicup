<?php

namespace Minicup\Model\Manager;


use LeanMapper\Connection;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\MatchTermRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Object;

class HandleManager extends Object
{
    /** @var MatchRepository */
    private $MR;

    /** @var TeamDataRefresher */
    private $TDR;

    /** @var  TagRepository */
    private $TR;

    /** @var TeamReplicator */
    private $replicator;

    /** @var ReorderManager */
    private $RM;

    /** @var Connection */
    private $connection;

    /** @var MatchTermRepository */
    private $MTR;

    /**
     * @param MatchRepository     $MR
     * @param TeamDataRefresher   $TDR
     * @param TeamReplicator      $replicator
     * @param ReorderManager      $RM
     * @param Connection          $connection
     * @param MatchTermRepository $MTR
     */
    public function __construct(TagRepository $TR, MatchRepository $MR, TeamDataRefresher $TDR, TeamReplicator $replicator, ReorderManager $RM, Connection $connection, MatchTermRepository $MTR)
    {
        $this->TR = $TR;
        $this->MR = $MR;
        $this->TDR = $TDR;
        $this->replicator = $replicator;
        $this->RM = $RM;
        $this->connection = $connection;
        $this->MTR = $MTR;
    }


    public function handleTags($params)
    {
        if (isset($params['term'])) {
            $tags = $this->TR->findLikeTerm($params['term']);
        } else {
            $tags = $this->TR->findAll();
        }
        $results = array();
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $results[] = array('id' => $tag->id, 'text' => $tag->name ? $tag->name : $tag->slug);
        }
        $this->presenter->sendJson(array('results' => $results));
    }


}
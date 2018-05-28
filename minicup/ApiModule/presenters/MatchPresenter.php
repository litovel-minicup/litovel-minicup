<?php

namespace Minicup\ApiModule\Presenters;


use Minicup\Model\Entity\Match;

class MatchPresenter extends BaseApiPresenter
{

    /**
     * @param Match $match
     * @throws \Nette\Application\AbortException
     */
    public function actionDetail(Match $match)
    {
        $this->sendJson(['match' => $match->serialize()]);
    }

    /**
     * @param Match $match
     * @throws \Nette\Application\AbortException
     */
    public function actionEvents(Match $match)
    {
        $data = [];
        foreach ($match->events as $event) {
            $data[] = $event->serialize();
        }
        $this->sendJson(['events' => $data]);
    }

}
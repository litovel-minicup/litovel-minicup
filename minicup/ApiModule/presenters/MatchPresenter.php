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

}
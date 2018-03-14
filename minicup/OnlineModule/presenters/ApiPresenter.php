<?php

namespace Minicup\OnlineModule\Presenters;

use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Player;
use Minicup\Model\Manager\OnlineManager;
use Minicup\Model\Repository\PlayerRepository;
use Nette\Application\Responses\JsonResponse;
use Nette\Utils\Json;

final class ApiPresenter extends BaseOnlinePresenter
{

    /** @var OnlineManager @inject */
    public $OM;

    /** @var PlayerRepository @inject */
    public $PR;

    /**
     * @param Match $match
     * @throws \Nette\Application\AbortException
     */
    public function actionState(Match $match)
    {
        $start = NULL;
        if ($match->secondHalfStart) {
            $start = $match->secondHalfStart->getTimestamp();
        } elseif ($match->firstHalfStart) {
            $start = $match->firstHalfStart->getTimestamp();
        }
        $this->sendResponse(new JsonResponse(
            [
                'id' => $match->id,
                'score' => [$match->scoreHome, $match->scoreAway],
                'halfStart' => $start,
                'halfIndex' => $match->getHalfIndex()
            ]

        ));

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
        $this->sendResponse(new JsonResponse(['events' => $data]));
    }

    /**
     * @param Match $match
     * @throws \LeanMapper\Exception\InvalidArgumentException
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Utils\JsonException
     */
    public function actionGoal(Match $match)
    {
        $params = Json::decode($this->getHttpRequest()->getRawBody());
        /** @var Player|NULL $player */
        $player = $this->PR->get(['id' => $params->playerId]);

        $goal = $this->OM->saveGoal(
            $match,
            $player
        );

        $this->sendResponse(new JsonResponse([
                'success' => true,
                'match' => $match->id,
                'goal' => $goal->id
            ]
        ));
    }

    /**
     * @param Match $match
     * @throws \Nette\Application\AbortException
     */
    public function actionStartHalf(Match $match)
    {
        $this->OM->startHalf($match);

        $this->sendResponse(new JsonResponse([
                'success' => true,
            ]
        ));
    }
}

<?php

namespace Minicup\ApiModule\Presenters;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Manager\LiveBarMessagesManager;
use Minicup\Model\Repository\MatchRepository;
use Nette\Application\AbortException;
use Nette\Utils\Html;

class NewsPresenter extends BaseApiPresenter
{
    /** @var LiveBarMessagesManager @inject */
    public $LBMR;

    /** @var string[] */
    public $liveBarMessages;

    /**
     * @param Category $category
     * @throws AbortException
     */
    public function actionLiveBar(Category $category)
    {
        $this->sendJson(['news' => $this->LBMR->generateMessages($category)]);
    }

}
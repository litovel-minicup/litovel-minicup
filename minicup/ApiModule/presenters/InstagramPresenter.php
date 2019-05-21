<?php

namespace Minicup\ApiModule\Presenters;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Manager\InstagramManager;
use Minicup\Model\Manager\LiveBarMessagesManager;
use Minicup\Model\Repository\MatchRepository;
use Nette\Application\AbortException;
use Nette\Utils\Html;

class InstagramPresenter extends BaseApiPresenter
{

    /** @var InstagramManager $IGM  @inject */
    public $IGM;

    /**
     * @param Category $category
     * @throws AbortException
     * @throws \Throwable
     */
    public function actionStories()
    {
        $this->sendJson(['stories' => $this->IGM->load()]);
    }

}
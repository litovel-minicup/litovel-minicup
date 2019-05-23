<?php

namespace Minicup\ApiModule\Presenters;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Manager\LiveBarMessagesManager;
use Minicup\Model\Repository\MatchRepository;
use Minicup\Model\Repository\TagRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\AbortException;
use Nette\Utils\Html;

class TagPresenter extends BaseApiPresenter
{
    /** @var TagRepository @inject */
    public $TR;

    /**
     * @throws AbortException
     */
    public function actionMainTags()
    {
        $this->sendJson(['tags' => array_map(function (Tag $t) {
            return [
                'id' => $t->id,
                'name' => $t->name,
            ];
        }, $this->TR->findMainTags($this->YR->getActualYear()))]);
    }

}
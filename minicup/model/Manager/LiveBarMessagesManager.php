<?php


namespace Minicup\Model\Manager;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Match;
use Minicup\Model\Repository\MatchRepository;
use Nette\SmartObject;
use Nette\Utils\Html;

class LiveBarMessagesManager
{
    use SmartObject;

    /** @var array|string[] */
    private $liveBarMessages;

    /** @var MatchRepository */
    private $MR;

    /**
     * @param string[]        $liveBarMessages
     * @param MatchRepository $MR
     */
    public function __construct(array $liveBarMessages, MatchRepository $MR)
    {
        $this->liveBarMessages = $liveBarMessages;
        $this->MR = $MR;
    }

    public function generateMessages(Category $category)
    {
        return array_merge(
            $this->liveBarMessages,
            array_values(array_map(function (Match $m) {
                return Html::el('span')->addHtml(
                    Html::el('strong')->setText($m->homeTeam->name)
                )->addText(
                    ' vs. '
                )->addHtml(
                    Html::el('strong')->setText($m->awayTeam->name)
                )->addText(
                    ' — '
                )->addText(
                    $m->scoreHome
                )->addText(
                    ':'
                )->addText(
                    $m->scoreAway
                )->__toString();
            }, $this->MR->findLastMatches($category)))
        );
    }


}
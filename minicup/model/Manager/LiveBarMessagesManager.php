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

    /** @var MatchManager */
    private $MM;

    /**
     * @param string[]        $liveBarMessages
     * @param MatchRepository $MR
     * @param MatchManager    $MM
     */
    public function __construct(array $liveBarMessages, MatchRepository $MR, MatchManager $MM)
    {
        $this->liveBarMessages = $liveBarMessages;
        $this->MR = $MR;
        $this->MM = $MM;
    }

    public function generateMessages(Category $category)
    {
        $matches = $this->MM->isFinished($category) ? [] : $this->MR->findLastMatches($category, 8);
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
            }, $matches))
        );
    }


}
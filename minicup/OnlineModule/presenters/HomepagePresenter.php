<?php

namespace Minicup\OnlineModule\Presenters;

use Minicup\Model\Entity\Match;
use Minicup\Model\Repository\MatchRepository;

/**
 * Homepage presenter.
 */
final class HomepagePresenter extends BaseOnlinePresenter
{
    /** @var MatchRepository */
    public $MR;

    public function renderDefault()
    {
        $this->template->matches = $this->category->matches;
    }

    public function actionWrite(Match $match)
    {
        $this->template->match = $match;
    }

}

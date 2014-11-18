<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Match;

/**
 * IOnlineComponentFactory
 */
interface IOnlineReportComponentFactory
{
    /**
     * @param $match Match
     * @return OnlineReportComponent
     */
    public function create($match);

}

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
     * @return \Minicup\Components\OnlineReportComponent
     */
    public function create($match);

}

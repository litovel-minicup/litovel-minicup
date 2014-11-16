<?php

namespace Minicup\Components;

/**
 * IOnlineComponentFactory
 */
interface IOnlineReportComponentFactory
{
    /**
     * @return \Minicup\Components\OnlineReportComponent
     */
    public function create();

}

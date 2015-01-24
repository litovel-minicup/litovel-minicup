<?php
namespace Minicup\Components;


use Minicup\Model\Entity\StaticContent;

interface IStaticContentComponentFactory
{
    /**
     * @param StaticContent $content
     * @return StaticContentComponent
     */
    public function create(StaticContent $content);
}
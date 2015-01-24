<?php

namespace Minicup\Components;


use Minicup\Model\Entity\StaticContent;
use Minicup\Model\Repository\StaticContentRepository;

class StaticContentComponent extends BaseComponent
{
    /** @var StaticContentRepository */
    private $SCR;

    /** @var \Texy */
    private $texy;

    /** @var StaticContent */
    private $content;

    public function __construct(StaticContent $content, StaticContentRepository $SCR, \Texy $texy)
    {
        parent::__construct();
        $this->SCR = $SCR;
        $this->texy = $texy;
        $this->content = $content;
    }


    public function render()
    {
        $output = $this->texy->process($this->content->content);
        dump($output);
        $this->template->content = $output;
        $this->template->render();
    }
}
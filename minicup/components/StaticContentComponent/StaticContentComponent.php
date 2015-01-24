<?php

namespace Minicup\Components;


use Minicup\Misc\Texy;
use Minicup\Model\Entity\StaticContent;
use Minicup\Model\Repository\StaticContentRepository;
use Nextras\Application\LinkFactory;

class StaticContentComponent extends BaseComponent
{
    /** @var StaticContentRepository */
    private $SCR;

    /** @var Texy */
    private $texy;

    /** @var StaticContent */
    private $content;

    /**
     * @param StaticContent $content
     * @param StaticContentRepository $SCR
     * @param \Texy $texy
     * @param LinkFactory $linkFactory
     */
    public function __construct(StaticContent $content, StaticContentRepository $SCR, Texy $texy)
    {
        parent::__construct();
        $this->SCR = $SCR;
        $this->texy = $texy;
        $this->content = $content;
    }


    public function render()
    {
        $this->template->content = $this->texy->process($this->content->content);
        $this->template->render();
    }
}
<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;
use Minicup\Model\Repository\TeamRepository;
use Nette\Application\UI\Control;

class ListOfTeamsComponent extends BaseComponent
{
    /** @var  TeamRepository */
    private $TR;

    public function __construct(TeamRepository $TR)
    {
        parent::__construct();
        $this->TR = $TR;
    }

    public function render(Category $category)
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/ListOfTeamsComponent.latte');
        $template->category = $category;
        $template->teams = $category->teams;
        $template->render();
    }
}
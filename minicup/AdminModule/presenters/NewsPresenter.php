<?php

namespace Minicup\AdminModule\Presenters;


use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\INewsFormComponentFactory;
use Minicup\Components\NewsFormComponent;
use Minicup\Model\Repository\NewsRepository;

class NewsPresenter extends BaseAdminPresenter
{
    /** @var Connection @inject */
    public $connection;

    /** @var INewsFormComponentFactory @inject */
    public $NFCF;

    /** @var  NewsRepository @inject */
    public $NR;

    public function renderDetail($id)
    {
        $this->template->news = $this->NR->get($id);
    }

    /**
     * @return NewsFormComponent
     */
    public function createComponentNewsFormComponent()
    {
        return $this->NFCF->create($this->NR->get($this->getParameter('id')));
    }

    /**
     * @param $name
     * @return Grid
     */
    protected function createComponentNewsGrid($name)
    {
        $g = new Grid($this, $name);
        $g->addColumnNumber('id', '#');
        $g->addColumnText('title', 'Titulek');
        $g->addColumnText('content', 'Obsah');
        $g->addActionHref('detail', 'Detail', 'News:detail', array('id' => 'id'));
        $g->setModel($this->connection->select('*')->from('[news]'));
        return $g;
    }
}
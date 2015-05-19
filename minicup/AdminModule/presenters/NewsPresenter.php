<?php

namespace Minicup\AdminModule\Presenters;


use Grido\Components\Columns\Date;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\INewsFormComponentFactory;
use Minicup\Components\NewsFormComponent;
use Minicup\Model\Repository\BaseRepository;
use Minicup\Model\Repository\NewsRepository;
use Nette\Utils\ArrayHash;

class NewsPresenter extends BaseAdminPresenter
{
    /** @var Connection @inject */
    public $connection;

    /** @var INewsFormComponentFactory @inject */
    public $NFCF;

    /** @var NewsRepository @inject */
    public $NR;

    public function renderDetail($id)
    {
        $this->template->news = $this->NR->get($id);
        $this->template->options = ArrayHash::from(array(
            array('link' => 'Homepage:default', 'name' => 'homepage (aktuality)', 'args' => array()),
            array('link' => 'Homepage:informations', 'name' => 'informace o turnaji', 'args' => array()),
            array('link' => 'Team:list', 'name' => 'seznam týmů dané kategorie', 'args' => array('category' => 'mladsi')),
            array('link' => 'Team:detail', 'name' => 'detail týmu dané kategorie', 'args' => array('category' => 'mladsi', 'team' => 'tatran-litovel')),
            array('link' => 'Match:list', 'name' => 'seznam zápasů dané kategorie', 'args' => array('category' => 'mladsi')),
            array('link' => 'Stats:default', 'name' => 'tabulka turnaje dané kategorie', 'args' => array('category' => 'mladsi')),
            array('link' => 'Gallery:default', 'name' => 'výchozí stránka fotogalerie', 'args' => array()),
            array('link' => 'Gallery:tags', 'name' => 'interaktivní výběr fotogalerie', 'args' => array()),
            //array('link' => 'Gallery:detail', 'name' => 'detail tagu fotogalerie (pouze hlavní tagy)', 'args' => array('tag' => 'vyhlaseni-turnaje')),
            array('link' => 'Homepage:sponsors', 'name' => 'sponzoři turnaje', 'args' => array())
        ));
    }

    /**
     * @return NewsFormComponent
     */
    public function createComponentNewsFormComponent()
    {
        $newsFormComponent = $this->NFCF->create($this->NR->get($this->getParameter('id')));
        $presenter = $this;
        $form = $newsFormComponent['newsForm'];
        $form->onSuccess[] = function () use ($presenter) {
            $presenter->redirect('News:');
        };
        return $newsFormComponent;
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
        $g->addColumnDate('added', 'Přidána', Date::FORMAT_DATETIME)->setDefaultSort(BaseRepository::ORDER_DESC);
        $g->addActionHref('detail', 'Detail', 'News:detail', array('id' => 'id'));
        $g->setModel($this->connection->select('*')->from('[news]'));
        return $g;
    }
}

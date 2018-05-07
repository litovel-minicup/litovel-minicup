<?php

namespace Minicup\AdminModule\Presenters;


use Grido\Components\Columns\Date;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\INewsFormComponentFactory;
use Minicup\Components\NewsFormComponent;
use Minicup\Misc\GridHelpers;
use Minicup\Model\Repository\BaseRepository;
use Minicup\Model\Repository\NewsRepository;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;

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
        $this->template->options = ArrayHash::from([
            ['link' => 'Homepage:default', 'name' => 'homepage (aktuality)', 'args' => []],
            ['link' => 'Homepage:informations', 'name' => 'informace o turnaji', 'args' => []],
            ['link' => 'Team:list', 'name' => 'seznam týmů dané kategorie', 'args' => ['category' => 'mladsi']],
            // ['link' => 'Team:detail', 'name' => 'detail týmu dané kategorie', 'args' => ['category' => 'mladsi', 'team' => 'tatran-litovel']],
            ['link' => 'Match:default', 'name' => 'seznam zápasů dané kategorie', 'args' => ['category' => 'mladsi']],
            ['link' => 'Stats:default', 'name' => 'tabulka turnaje dané kategorie', 'args' => ['category' => 'mladsi']],
            ['link' => 'Gallery:default', 'name' => 'výchozí stránka fotogalerie', 'args' => []],
            ['link' => 'Gallery:tags', 'name' => 'interaktivní výběr fotogalerie', 'args' => []],
            //array('link' => 'Gallery:detail', 'name' => 'detail tagu fotogalerie (pouze hlavní tagy)', 'args' => array('tag' => 'vyhlaseni-turnaje')),
            ['link' => 'Homepage:sponsors', 'name' => 'sponzoři turnaje', 'args' => []]
        ]);
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
        $g = new Grid();
        $g->addColumnNumber('id', '#');
        $g->addColumnText('title', 'Titulek')->setEditableCallback(GridHelpers::getEditableCallback('title', $this->NR));
        $g->addColumnText('content', 'Obsah');
        $g->addColumnNumber('texy', 'Texy?')->setReplacement([
            0 => Html::el('i')->addAttributes(['class' => 'glyphicon glyphicon-remove']),
            1 => Html::el('i')->addAttributes(['class' => 'glyphicon glyphicon-ok'])
        ]);
        $g->addColumnNumber('published', 'Publikována?')->setReplacement([
            0 => Html::el('i')->addAttributes(['class' => 'glyphicon glyphicon-remove']),
            1 => Html::el('i')->addAttributes(['class' => 'glyphicon glyphicon-ok'])
        ]);
        $g->addColumnDate('added', 'Přidána', Date::FORMAT_DATETIME)->setDefaultSort(BaseRepository::ORDER_DESC);
        $g->addActionHref('detail', 'Detail', 'News:detail', ['id' => 'id']);
        $NR = $this->NR;
        $g->addActionEvent('delete', 'Smazat', function ($id) use ($NR) {
            $NR->delete($id);
        })->setConfirm('Jsi si jistý?');
        $g->setModel($this->connection->select('*')->from('[news]')->where('[year_id] = ', $this->category->year->id));
        return $g;
    }
}

<?php

namespace Minicup\AdminModule\Presenters;

use Grido\Components\Filters\Filter;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\IPhotoListComponentFactory;
use Minicup\Components\IPhotoUploadComponentFactory;
use Minicup\Components\ITagFormComponentFactory;
use Minicup\Components\PhotoListComponent;
use Minicup\Components\PhotoUploadComponent;
use Minicup\Model\Manager\ReorderManager;
use Minicup\Model\Repository\TagRepository;
use Nette\Utils\Html;

final class PhotoPresenter extends BaseAdminPresenter
{
    /** @var ReorderManager @inject */
    public $reorder;

    /** @var IPhotoUploadComponentFactory @inject */
    public $PUC;

    /** @var TagRepository @inject */
    public $TR;

    /** @var Connection @inject */
    public $connection;

    /** @var ITagFormComponentFactory @inject */
    public $TFCF;

    /** @var IPhotoListComponentFactory @inject */
    public $PLCF;

    public function renderTagDetail($id)
    {
        $this->template->tag = $this->TR->get($id);
    }

    protected function createComponentTagsGrid($name)
    {
        $g = new Grid($this, $name);
        $g->setFilterRenderType(Filter::RENDER_INNER);
        $g->addColumnNumber('id', '#');
        $g->addColumnText('name', 'Název');
        $g->addColumnText('slug', 'Slug');
        $main = $g->addColumnText('is_main', 'Hlavní');
        $main->setReplacement(array(
            0 => Html::el('i')->addAttributes(array('class' => "glyphicon glyphicon-remove")),
            1 => Html::el('i')->addAttributes(array('class' => "glyphicon glyphicon-ok"))
        ));
        $g->addActionHref('detail', 'Detail', 'Photo:tagDetail', array('id' => 'id'));
        $g->setModel($this->connection->select('*')->from('[tag]')->orderBy('[is_main] DESC, [name] ASC'));
        return $g;
    }

    /**
     * @return \Minicup\Components\TagFormComponent
     */
    protected function createComponentTagFormComponent()
    {
        $presenter = $this;
        $tagFormComponent = $this->TFCF->create($this->TR->get($this->getParameter('id')));
        $tagFormComponent['tagForm']->onSuccess[] = function () use ($presenter) {
            /** @var Grid $grid */
            $grid = $presenter['tagsGrid'];
            $grid->reload();
        };
        return $tagFormComponent;
    }

    /**
     * @return PhotoListComponent
     * @throws EntityNotFoundException
     */
    protected function createComponentPhotoListComponent()
    {
        return $this->PLCF->create($this->TR->get($this->getParameter('id'))->photos);
    }

    /**
     * @return PhotoUploadComponent
     */
    public function createComponentPhotoUploadComponent()
    {
        return $this->PUC->create();
    }
}

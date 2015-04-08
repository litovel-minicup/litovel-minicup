<?php

namespace Minicup\AdminModule\Presenters;

use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\IPhotoListComponentFactory;
use Minicup\Components\IPhotoUploadComponentFactory;
use Minicup\Components\ITagFormComponentFactory;
use Minicup\Components\PhotoListComponent;
use Minicup\Components\PhotoUploadComponent;
use Minicup\Model\Manager\ReorderManager;
use Minicup\Model\Repository\EntityNotFoundException;
use Minicup\Model\Repository\TagRepository;

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

    /**
     * @return PhotoUploadComponent
     */
    public function createComponentPhotoUploadComponent()
    {
        return $this->PUC->create();
    }

    protected function createComponentTagsGrid($name) {
        $g = new Grid($this, $name);
        $g->addColumnNumber('id', '#');
        $g->addColumnText('name', 'Název');
        $g->addColumnText('slug', 'Slug');
        $g->addActionHref('detail', 'Detail', 'Photo:tagDetail', array('id' => 'id'));
        $g->setModel($this->connection->select('*')->from('[tag]'));
        return $g;
    }

    public function renderTagDetail($id)
    {
    	$this->template->tag = $this->TR->get($id);
    }

    /***/
    protected function createComponentTagFormComponent()
    {
        return $this->TFCF->create($this->TR->get($this->getParameter('id')));
    }

    /**
     * @return PhotoListComponent
     * @throws EntityNotFoundException
     */
    protected function createComponentPhotoListComponent()
    {
        return $this->PLCF->create($this->TR->get($this->getParameter('id'))->photos);
    }
}

<?php

namespace Minicup\AdminModule\Presenters;

use Dibi\Row;
use Grido\Components\Filters\Filter;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Components\AdminPhotoListComponent;
use Minicup\Components\IAdminPhotoListComponentFactory;
use Minicup\Components\IPhotoEditComponentFactory;
use Minicup\Components\IPhotoListComponentFactory;
use Minicup\Components\IPhotoUploadComponentFactory;
use Minicup\Components\ITagFormComponentFactory;
use Minicup\Components\PhotoEditComponent;
use Minicup\Components\PhotoListComponent;
use Minicup\Components\PhotoUploadComponent;
use Minicup\Components\TagFormComponent;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Manager\ReorderManager;
use Minicup\Model\Repository\BaseRepository;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Application\AbortException;
use Nette\Utils\Html;

final class PhotoPresenter extends BaseAdminPresenter
{
    /** @var ReorderManager @inject */
    public $reorder;

    /** @var IPhotoUploadComponentFactory @inject */
    public $PUC;

    /** @var TagRepository @inject */
    public $TR;

    /** @var PhotoRepository @inject */
    public $PR;

    /** @var Connection @inject */
    public $connection;

    /** @var ITagFormComponentFactory @inject */
    public $TFCF;

    /** @var IPhotoListComponentFactory @inject */
    public $PLCF;

    /** @var IAdminPhotoListComponentFactory @inject */
    public $APLCF;

    /** @var IPhotoEditComponentFactory @inject */
    public $PECF;

    /** @var int @persistent */
    public $id = 0;

    public function renderTagDetail($id)
    {
        $this->template->tag = $this->TR->get($id);
    }

    /**
     * @return PhotoUploadComponent
     */
    public function createComponentPhotoUploadComponent()
    {
        return $this->PUC->create();
    }

    /**
     * @return AdminPhotoListComponent
     */
    public function createComponentAdminPhotoListComponent()
    {
        return $this->APLCF->create();
    }

    /**
     * Provide data about tags for select2 by optional term in post parameters
     *
     * @throws AbortException
     */
    public function handleTags()
    {
        $term = $this->request->getPost('term');
        if ($term) {
            $tags = $this->TR->findLikeTerm($term);
        } else {
            $tags = $this->TR->findAll();
        }
        $results = array();
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $results[] = array('id' => $tag->id, 'text' => $tag->name ?: $tag->slug);
        }
        $this->presenter->sendJson(array('results' => $results));
    }

    /**
     * @param string $name
     * @return Grid
     */
    protected function createComponentTagsGrid($name)
    {
        $TR = $this->TR;
        $PR = $this->PR;
        $presenter = $this;
        $g = new Grid($this, $name);
        $g->setFilterRenderType(Filter::RENDER_INNER);

        $g->addColumnNumber('id', '#');

        $g->addColumnText('name', 'Název')->setFilterText();

        $g->addColumnText('slug', 'Slug')->setFilterText();

        $g->addColumnText('count_of_photos', 'Počet fotek');

        $g->addColumnText('is_main', 'Hlavní')->setReplacement(array(
            0 => Html::el('i')->addAttributes(array('class' => 'glyphicon glyphicon-remove')),
            1 => Html::el('i')->addAttributes(array('class' => 'glyphicon glyphicon-ok'))
        ))->setDefaultSort(BaseRepository::ORDER_DESC);

        $g->addColumnText('main_photo', 'Hlavní fotka')->setCustomRender(function (Row $row) use ($presenter, $PR) {
            /** @var Photo $photo */
            $photo = $PR->get($row->main_photo_id, FALSE);
            if ($photo) {
                $src = $presenter->link(':Media:mini', array($photo->filename));
                return Html::el('img', array('src' => $src));
            }
            return ' - ';
        });

        $g->addActionHref('detail', 'Detail', 'Photo:tagDetail', array('id' => 'id'));

        $g->addActionEvent('delete', 'Smazat', function ($id) use ($TR) {
            /** @var Tag $tag */
            $tag = $TR->get($id);
            $tag->removeAllPhotos();
            $TR->delete($tag);
        })->setConfirm('Opravdu smazat tag a všechny jeho vazby?');

        $g->addActionEvent('is_main', 'changeMain', function ($id) use ($TR) {
            /** @var Tag $tag */
            $tag = $TR->get($id);
            $tag->isMain = $tag->isMain ? 0 : 1;
            $TR->persist($tag);
        })->setCustomRender(function (Row $row, Html $element) {
            return $element->setText(!$row->is_main ? 'Nastavit jako HLAVNÍ' : 'Nastavit jako VEDLEJŠÍ');
        });

        $g->setModel(
            $model = $this->connection->select('*')->from('[tag]')->select(
                $this->connection->select('COUNT(*)')->from('[photo_tag]')->where('[tag_id] = [id]'), 'count_of_photos')
        );
        return $g;
    }

    /**
     * @return TagFormComponent
     */
    protected function createComponentTagFormComponent()
    {
        $presenter = $this;
        /** @var TagFormComponent $tagFormComponent */
        $tagFormComponent = $this->TFCF->create($this->TR->get($this->getParameter('id')));
        $tagFormComponent['tagForm']->onSuccess[] = function () use ($presenter) {
            /** @var Grid $grid */
            $grid = $presenter['tagsGrid'];
            $grid->reload();
        };
        if ($this->action === 'tagDetail') {
            $tagFormComponent->view = 'full';
        }
        return $tagFormComponent;
    }

    /**
     * @return PhotoListComponent
     */
    protected function createComponentPhotoListComponent()
    {
        return $this->PLCF->create($this->TR->get($this->getParameter('id'))->photos, NULL);
    }

    /**
     * @return PhotoEditComponent
     */
    protected function createComponentPhotoEditComponent()
    {
        $photoEdit = $this->PECF->create($this->PR->get($this->getParameter('id'), FALSE));
        if ($this->action === 'photoDetail') {
            $that = $this;
            $photoEdit->onDelete[] = function (Photo $photo) use ($that) {
                $that->redirect('photos');
            };
        }
        return $photoEdit;
    }

}

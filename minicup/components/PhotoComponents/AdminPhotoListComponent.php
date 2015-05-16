<?php

namespace Minicup\Components;

use Grido\Components\Columns\Date;
use Grido\Components\Filters\Filter;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\Multiplier;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Utils\Html;
use Nette\Utils\Random;

class AdminPhotoListComponent extends BaseComponent
{
    /** @var SessionSection */
    private $session;

    /** @var IPhotoEditComponentFactory */
    private $PECF;

    /** @var PhotoRepository */
    private $PR;

    /** @var TagRepository */
    private $TR;

    /** @var  Connection */
    private $connection;

    /** @var Tag[] */
    private $tags = array();

    /** @var Photo[] */
    private $photos = array();

    /** @var string */
    private $id;

    /** @var bool */
    private $allPhotos;

    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var PhotoManager */
    private $PM;

    public function __construct(Session $session,
                                IPhotoEditComponentFactory $PECF,
                                PhotoRepository $PR,
                                TagRepository $TR,
                                Connection $connection,
                                LinkGenerator $linkGenerator,
                                PhotoManager $PM)
    {
        $this->session = $session->getSection('minicup');
        $this->PECF = $PECF;
        $this->PR = $PR;
        $this->TR = $TR;
        $this->connection = $connection;
        $this->linkGenerator = $linkGenerator;
        $this->PM = $PM;
        if (isset($this->session->adminPhotoList)) {
            $this->id = $this->session->adminPhotoList;
        } else {
            $this->id = Random::generate(10);
        }
        if (isset($this->session->allPhotos)) {
            $this->allPhotos = $this->session->allPhotos;
        } else {
            $this->allPhotos = FALSE;
        }
        $this->session->adminPhotoList = $this->id;
        $this->session->allPhotos = $this->allPhotos;
        $this->session[$this->id] = $this->session[$this->id] ? $this->session[$this->id] : array();
        $this->photos = $this->PR->findByTags($this->TR->findByIds($this->session[$this->id]));
    }

    public function render()
    {
        if (!$this->photos && !$this->session[$this->id]) {
            $this->view = "default";
        } else {
            $this->view = "list";
        }
        $this->template->tags = $this->TR->findAll();
        $this->template->selectedTags = (array)$this->session[$this->id];
        $this->template->photos = $this->photos;
        parent::render();
    }

    public function handleRefresh()
    {
        $params = $this->presenter->request->parameters;
        $this->session[$this->id] = $params['ids'];
        $this->tags = $this->TR->findByIds((array)$this->session[$this->id]);
        $this->photos = $this->PR->findByTags($this->tags);
        $this->session->allPhotos = FALSE;
        $this->redrawControl('photo-list');
    }

    public function handleActivePhotos()
    {
        unset($this->session->adminPhotoList);
        unset($this->session[$this->id]);
        $this->session->allPhotos = FALSE;
        $this->redirect('this');
    }

    public function handleAllPhotos()
    {
        unset($this->session->adminPhotoList);
        unset($this->session[$this->id]);
        $this->session->allPhotos = TRUE;
        $this->redirect('this');
    }

    public function createComponentPhotosGrid($name)
    {
        $PR = $this->PR;
        $PM = $this->PM;
        $linkGenerator = $this->linkGenerator;
        $g = new Grid($this, $name);
        $g->setFilterRenderType(Filter::RENDER_INNER);
        $g->addColumnNumber('id', '#');
        $g->addColumnText('filename', 'Jméno');
        $g->addActionHref('detail', 'Detail', 'Photo:photoDetail', array('id' => 'id'));
        $g->addActionEvent('delete', 'Smazat', function ($id) use ($PM, $PR, $g) {
            $PM->delete($PR->get($id, FALSE), FALSE);
            $g->reload();
        })->setConfirm('Smazat?')->getElementPrototype()->addAttributes(array("class" => "ajax"));
        $g->addActionEvent('changeView', 'Zobrazit/Skrýt', function ($id) use ($PR, $g) {
            /** @var Photo $photo */
            $photo = $PR->get($id);
            $photo->active = $photo->active ? 0 : 1;
            $PR->persist($photo);
            $g->reload();
        })->setCustomRender(function (\DibiRow $row, Html $element) {
            return $element->setText($row['active'] ? 'Skrýt' : 'Zobrazit');
        })->getElementPrototype()->addAttributes(array("class" => "ajax"));
        $g->addColumnText('thumb', 'Náhled')->setCustomRender(function (\DibiRow $row) use ($linkGenerator) {
            return Html::el('img', array("src" => $linkGenerator->link("Media:mini", array($row->filename))));
        });
        $g->addColumnDate('taken', "Pořízena", Date::FORMAT_DATETIME)->setSortable();
        if ($this->allPhotos) {
            $active = $g->addColumnNumber('active', 'Aktivní');
            $active->setReplacement(array(
                0 => Html::el('i')->addAttributes(array('class' => "glyphicon glyphicon-remove")),
                1 => Html::el('i')->addAttributes(array('class' => "glyphicon glyphicon-ok"))
            ));
            $g->setModel($this->connection->select('*')->from('[photo]')); /**  ->orderBy('[added] DESC')); */
        } else {
            $g->setModel($this->connection->select('*')->from('[photo]')->where('[active] = 1')->orderBy('[added] DESC'));
        }
    }

    /**
     * @return PhotoEditComponent
     */
    protected function createComponentPhotoEditComponent()
    {
        $PECF = $this->PECF;
        $PR = $this->PR;
        $APLC = $this;
        return new Multiplier(function ($id) use ($PECF, $PR, $APLC) {
            $photo = $PR->get($id);
            $photoEdit = $PECF->create($photo);
            $photoEdit->onDelete[] = function (Photo $photo) use ($APLC, $PR) {
                $APLC->photos = $APLC->PR->findByIds(array_diff(array_map(function (Photo $photo) {
                    return $photo->id;
                }, $APLC->photos), array($photo->id)));
                $APLC->redrawControl('photo-list');
            };
            return $photoEdit;
        });
    }
}


interface IAdminPhotoListComponentFactory
{
    /**
     * @return AdminPhotoListComponent
     */
    public function create();

}
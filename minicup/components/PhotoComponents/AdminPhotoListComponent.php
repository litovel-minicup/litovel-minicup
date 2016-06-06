<?php

namespace Minicup\Components;

use Dibi\Row;
use Grido\Components\Columns\Date;
use Grido\Grid;
use LeanMapper\Connection;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Entity\Year;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\BaseRepository;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Minicup\Presenters\BasePresenter;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\Multiplier;
use Nette\ComponentModel\IComponent;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Utils\Html;
use Nette\Utils\Random;

interface IAdminPhotoListComponentFactory
{
    /**
     * @return AdminPhotoListComponent
     */
    public function create();

}

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
    private $tags = [];

    /** @var Photo[] */
    private $photos = [];

    /** @var string */
    private $id;

    /** @var bool */
    private $allPhotos;

    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var PhotoManager */
    private $PM;

    /** @var Year */
    private $year;

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
        $this->allPhotos = FALSE;
        if (isset($this->session->allPhotos)) {
            $this->allPhotos = $this->session->allPhotos;
        }
        $this->session->adminPhotoList = $this->id;
        $this->session->allPhotos = $this->allPhotos;
        $this->session[$this->id] = $this->session[$this->id] ?: [];
        $this->photos = $this->PR->findByTags($this->TR->findByIds($this->session[$this->id]));
        parent::__construct();
    }

    public function render()
    {
        $this->view = 'list';
        if (!$this->photos && !$this->session[$this->id]) {
            $this->view = 'default';
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
        if ($this->presenter->isAjax()) {
            $this->redrawControl('photo-list');
        } else {
            $this->redirect('this');
        }
    }

    public function handleActivePhotos()
    {
        unset($this->session->adminPhotoList, $this->session[$this->id]);
        $this->session->allPhotos = FALSE;
        if ($this->presenter->isAjax()) {
            $this->redrawControl('photo-list');
        } else {
            $this->redirect('this');
        }
    }

    public function handleAllPhotos()
    {
        unset($this->session->adminPhotoList, $this->session[$this->id]);
        $this->session->allPhotos = TRUE;
        if ($this->presenter->isAjax()) {
            $this->redrawControl('photo-list');
        } else {
            $this->redirect('this');
        }
    }

    public function handleUntaggedPhotos()
    {
        // TODO sessions?
        $this->photos = $this->PR->findUntaggedPhotos();
        if ($this->presenter->isAjax()) {
            $this->redrawControl('photo-list');
        } else {
            $this->redirect('this');
        }
    }

    public function createComponentPhotoGrid($name)
    {
        $PR = $this->PR;
        $PM = $this->PM;
        $linkGenerator = $this->linkGenerator;
        $model = $this->connection->select('[photo.*]')
            ->from('[photo]')
            ->rightJoin('[photo_tag]')->on('[photo_tag.photo_id] = [photo.id]')
            ->rightJoin('[tag]')->on('[photo_tag.tag_id] = [tag.id]')
            ->where('[tag.year_id] =', $this->year->id)
            ->where('[photo.id] IS NOT NULL')
            ->groupBy('id')
            ->select($this->connection->select('COUNT(*)')->from('[photo_tag]')->where('[photo_id] = [photo.id]'), 'count_of_tags');
        $g = new Grid($this, $name);

        $g->addColumnNumber('id', '#');

        $g->addColumnText('filename', 'Jméno souboru')->setFilterText();

        $g->addActionHref('detail', 'Detail fotky', 'Photo:photoDetail', ['id' => 'id']);

        $g->addActionEvent('delete', 'Smazat z disku', function ($id) use ($PM, $PR) {
            $PM->delete($PR->get($id, FALSE), FALSE);
        })->setConfirm('Opravdu chcete smazat fotku i z disku?');

        $g->addActionEvent('changeView', 'Zobrazit/Skrýt', function ($id) use ($PR) {
            /** @var Photo $photo */
            $photo = $PR->get($id);
            $photo->active = $photo->active ? 0 : 1;
            $PR->persist($photo);
        })->setCustomRender(function (Row $row, Html $element) {
            return $element->setText($row['active'] ? 'Skrýt' : 'Zobrazit');
        });

        $g->addColumnText('thumb', 'Náhled')->setCustomRender(function (Row $row) use ($linkGenerator) {
            return Html::el('img', ['src' => $linkGenerator->link('Media:mini', [$row->filename])]);
        });

        $g->addColumnDate('taken', 'Pořízena', Date::FORMAT_DATETIME);

        $g->addColumnDate('added', 'Přidána', Date::FORMAT_DATETIME)->setDefaultSort(BaseRepository::ORDER_DESC);

        $g->addColumnText('count_of_tags', 'Počet tagů')->setSortable();

        if ($this->allPhotos) {
            $active = $g->addColumnNumber('active', 'Aktivní');
            $active->setReplacement([
                0 => Html::el('i')->addAttributes(['class' => 'glyphicon glyphicon-remove']),
                1 => Html::el('i')->addAttributes(['class' => 'glyphicon glyphicon-ok'])
            ]);
        } else {
            $model->where('[active] = 1');
        }
        $g->setModel($model);
        return $g;
    }

    /**
     * @param IComponent $presenter
     */
    protected function attached($presenter)
    {
        /** @var BasePresenter $presenter */
        parent::attached($presenter);
        $this->year = $presenter->category->year;
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
                }, $APLC->photos), [$photo->id]));
                $APLC->redrawControl('photo-list');
            };
            return $photoEdit;
        });
    }
}
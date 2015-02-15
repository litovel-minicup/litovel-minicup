<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Photo;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Application\UI\Multiplier;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Utils\Random;

// TODO: add forms to add tags with autocompleting thru ajax
class PhotoUploadComponent extends BaseComponent
{
    /** @var Request */
    private $httpRequest;

    /** @var SessionSection */
    private $session;

    /** @var PhotoRepository */
    private $PR;

    /** @var TagRepository */
    private $TR;

    /** @var PhotoManager */
    private $PM;

    /** @var IPhotoEditComponentFactory */
    private $PECF;

    /** @var String */
    private $uploadId;

    /** @var Photo[] */
    private $photos = array();

    /**
     * @param string $wwwPath
     * @param Session $session
     * @param Request $httpRequest
     * @param PhotoRepository $PR
     * @param TagRepository $TR
     * @param PhotoManager $PM
     * @param IPhotoEditComponentFactory $PECF
     */
    public function __construct($wwwPath, Session $session, Request $httpRequest, PhotoRepository $PR, TagRepository $TR, PhotoManager $PM, IPhotoEditComponentFactory $PECF)
    {
        $this->httpRequest = $httpRequest;
        $this->session = $session->getSection('photoUpload');
        $this->TR = $TR;
        $this->PR = $PR;
        $this->PM = $PM;
        $this->PECF = $PECF;
        $uploadId = $this->session['uploadId'];
        if ($uploadId) {
            $this->uploadId = $uploadId;
        } else {
            $this->uploadId = Random::generate(20);
        }
        $this->session['uploadId'] = $this->uploadId;
    }

    public function render()
    {
        if (!isset($this->template->photos)) {
            $ids = is_array($this->session[$this->uploadId]) ? $this->session[$this->uploadId] : array();
            $this->template->photos = $this->PR->findByIds($ids);
        }
        $this->template->uploadId = $this->uploadId;
        parent::render();
    }

    public function handleUpload()
    {
        $photos = $this->PM->save($this->httpRequest->files, $this->uploadId);
        $ids = is_array($this->session[$this->uploadId]) ? $this->session[$this->uploadId] : array();
        foreach ($photos as $photo) {
            $ids[] = $photo->id;
        }
        $this->session[$this->uploadId] = $ids;
        $this->photos = $this->template->photos = $this->PR->findByIds($ids);
        $this->redrawControl('photos-list');
    }

    /**
     * @return PhotoEditComponent
     */
    protected function createComponentPhotoEditComponent()
    {
        $PECF = $this->PECF;
        $PR = $this->PR;
        $PUC = $this;
        return new Multiplier(function ($id) use ($PECF, $PR, $PUC) {
            $photo = $PR->get($id);
            $PEC = $PECF->create($photo);
            $PEC->onDelete[] = function (Photo $photo) use ($PUC) {
                $PUC->redrawControl('photos-list');
            };
            return $PEC;
        });
    }

}
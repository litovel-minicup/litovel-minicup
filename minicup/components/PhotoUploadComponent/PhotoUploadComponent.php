<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Photo;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Http\Request;
use Nette\Utils\Random;

class PhotoUploadComponent extends BaseComponent
{
    /** @var Request */
    private $httpRequest;

    /** @var PhotoRepository */
    private $PR;

    /** @var TagRepository */
    private $TR;

    /** @var String */
    private $uploadId;

    /** @var PhotoManager */
    private $PM;

    /** @var Photo[] */
    private $photos = array();

    /**
     * @param string $wwwPath
     * @param Request $httpRequest
     * @param PhotoRepository $PR
     * @param TagRepository $TR
     * @param PhotoManager $PM
     */
    public function __construct($wwwPath, Request $httpRequest, PhotoRepository $PR, TagRepository $TR, PhotoManager $PM)
    {
        $this->httpRequest = $httpRequest;
        $this->TR = $TR;
        $this->PR = $PR;
        $this->PM = $PM;
        $uploadId = $this->httpRequest->getPost('uploadId', NULL);
        if ($uploadId) {
            $this->uploadId = $uploadId;
        } else {
            $this->uploadId = Random::generate(20);
        }
    }

    public function render()
    {
        if (!isset($this->template->photos)) {
            $this->template->photos = $this->photos;
        }
        $this->template->uploadId = $this->uploadId;
        parent::render();
    }

    public function handleUpload()
    {
        $this->template->photos = $this->photos = $this->PM->save($this->httpRequest->files, $this->uploadId);
        $this->redrawControl('photos');
    }
}
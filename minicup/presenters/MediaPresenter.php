<?php

namespace Minicup\Presenters;


use Minicup\Model\Manager\PhotoManager;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Presenter;
use Nette\FileNotFoundException;

class MediaPresenter extends Presenter
{
    /** @var PhotoManager @inject */
    public $PM;

    public function actionThumb($slug)
    {
        try {
            $requested = $this->PM->getInFormat($slug, PhotoManager::PHOTO_THUMB);
        } catch (FileNotFoundException $e) {
            throw new BadRequestException($e->getMessage());
        }
        $this->sendResponse(new FileResponse($requested));
    }

    public function actionMedium($slug)
    {
        try {
            $requested = $this->PM->getInFormat($slug, PhotoManager::PHOTO_MEDIUM);
        } catch (FileNotFoundException $e) {
            throw new BadRequestException($e->getMessage());
        }
        $this->sendResponse(new FileResponse($requested));
    }
}
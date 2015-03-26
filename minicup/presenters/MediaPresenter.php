<?php

namespace Minicup\Presenters;


use Minicup\Model\Manager\PhotoManager;
use Nette\Application;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Presenter;
use Nette\FileNotFoundException;

class MediaPresenter extends Presenter
{
    /** @var PhotoManager @inject */
    public $PM;

    public static function formatActionMethod($action)
    {
        if (isset(PhotoManager::$resolutions[$action])) {
            return "servePhoto";
        }
        return parent::formatActionMethod($action);
    }

    public function servePhoto($slug)
    {
        try {
            $requested = $this->PM->getInFormat($slug, $this->action);
        } catch (FileNotFoundException $e) {
            throw new BadRequestException($e->getMessage());
        }
        $this->sendResponse(new FileResponse($requested));
    }
}
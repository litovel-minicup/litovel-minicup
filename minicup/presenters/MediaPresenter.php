<?php

namespace Minicup\Presenters;


use Minicup\Model\Manager\PhotoManager;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Presenter;
use Nette\Utils\Image;
use Nette\Utils\UnknownImageFileException;

class MediaPresenter extends Presenter
{
    /** @var PhotoManager @inject */
    public $PM;

    public function actionThumb($slug)
    {
        $this->providePhoto($slug, "thumb", array(300, 300));
    }

    public function actionPhoto($slug)
    {
        $this->providePhoto($slug, "photo", array(NULL, NULL));
    }


    /**
     * @param string $slug
     * @param string $type
     * @param int[] $dimensions
     * @throws BadRequestException
     * @throws UnknownImageFileException
     */
    private function providePhoto($slug, $type, $dimensions)
    {
        $wwwDir = $this->context->parameters['wwwDir'];
        $requested = $wwwDir . "/media/$type/$slug";
        if (file_exists($requested)) {
            // never?
            $this->sendResponse(new FileResponse($requested));
        }
        $original = $wwwDir . "/media/original/$slug";
        if (file_exists($original)) {
            $image = Image::fromFile($original);
            $image->resize($dimensions[0], $dimensions[1], Image::FILL);
            $image->sharpen();
            $image->save($requested);
            $image->send();
            $this->sendResponse(new FileResponse($requested));
        }
        throw new BadRequestException('Requested photo not found!');
    }
}
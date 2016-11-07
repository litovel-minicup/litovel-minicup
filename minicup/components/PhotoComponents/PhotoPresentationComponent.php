<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Year;
use Minicup\Model\Repository\PhotoRepository;
use Nette\Application\LinkGenerator;

interface IPhotoPresentationComponentFactory
{
    /**
     * @param Year $year
     * @return PhotoPresentationComponent
     */
    public function create(Year $year);
}


class PhotoPresentationComponent extends BaseComponent
{
    /** @var Year */
    private $year;

    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var PhotoRepository */
    private $photoRepository;

    /**
     * @param Year            $year
     * @param LinkGenerator   $linkGenerator
     * @param PhotoRepository $photoRepository
     */
    public function __construct(Year $year,
                                LinkGenerator $linkGenerator,
                                PhotoRepository $photoRepository
    )
    {
        $this->year = $year;
        $this->photoRepository = $photoRepository;
        $this->linkGenerator = $linkGenerator;
        parent::__construct();
    }

    public function handleData()
    {
        $data = [];
        foreach ($this->photoRepository->findByYear($this->year) as $photo) {
            $data[] = $this->linkGenerator->link('Media:large', [$photo->filename]);
        }
        $this->presenter->sendJson($data);
    }
}
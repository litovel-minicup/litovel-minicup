<?php

namespace Minicup\Components;


use Minicup\AdminModule\Presenters\BaseAdminPresenter;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Manager\CacheManager;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Application\UI\Multiplier;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Utils\Image;
use Nette\Utils\ImageException;
use Nette\Utils\Json;
use Nette\Utils\Random;

interface IPhotoPutComponentFactory
{
    /** @return PhotoPutComponent */
    public function create();
}

class PhotoPutComponent extends BaseComponent
{
    /** @var int[] */
    public $photos = [];
    /** @var Request */
    private $request;
    /** @var SessionSection */
    private $session;
    /** @var PhotoRepository */
    private $PR;
    /** @var TagRepository */
    private $TR;
    /** @var PhotoManager */
    private $PM;
    /** @var String */
    private $uploadId;
    /** @var CacheManager */
    private $CM;

    /**
     * @param Session         $session
     * @param Request         $request
     * @param PhotoRepository $PR
     * @param TagRepository   $TR
     * @param PhotoManager    $PM
     * @param CacheManager    $CM
     */
    public function __construct(Session $session,
                                Request $request,
                                PhotoRepository $PR,
                                TagRepository $TR,
                                PhotoManager $PM,
                                CacheManager $CM)
    {
        $this->request = $request;
        $this->session = $session->getSection('photoUpload');
        $this->TR = $TR;
        $this->PR = $PR;
        $this->PM = $PM;
        $this->CM = $CM;
        $uploadId = $this->session['uploadId'];
        if ($uploadId) {
            $this->uploadId = $uploadId;
        } else {
            $this->uploadId = Random::generate(20);
        }
        $this->session['uploadId'] = $this->uploadId;
        $this->photos = (array)$this->session[$this->uploadId];
        parent::__construct();

    }

    public function render()
    {
        $this->template->photos = $this->PR->findByIds($this->photos);
        $this->template->uploadId = $this->uploadId;
        $this->session[$this->uploadId] = $this->photos;
        parent::render();
    }


    /**
     * @throws \LeanMapper\Exception\InvalidArgumentException
     * @throws \Nette\Application\AbortException
     */
    public function handleUpload()
    {
        bdump($this->presenter->getHttpRequest());
        try {
            $image = Image::fromString($this->presenter->getHttpRequest()->getRawBody());
        } catch (ImageException $e) {
            $this->presenter->sendJson([
                'success' => false,
            ]);
            return;
        }
        $photo = $this->PM->saveImage($image, $this->uploadId, $this->request->getPost('author'));
        $this->photos[] = $photo->id;
        $this->session[$this->uploadId] = $this->photos;
        $this->presenter->sendJson([
            'photo' => $photo->id,
            'success' => true,
        ]);
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function handleGetPhotos()
    {
        bdump($this->presenter->getHttpRequest());
        $photos = $this->PR->findByIds($this->photos);
        $this->presenter->sendJson([
            'photos' => array_map(function (Photo $p) {
                return [
                    'id' => $p->id,
                    'thumb' => $this->presenter->link(':Media:thumb', $p->filename),
                    'tags' => count($p->tags),
                ];
            }, $photos)
        ]);
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Utils\JsonException
     */
    public function handleUpdateTags()
    {
        $data = Json::decode($this->request->getRawBody(), Json::FORCE_ARRAY);
        $photos = $data['photos'];
        $tags = $data['tags'];

        $tagEntities = [];
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $tag = $this->TR->get($tag);
            if (!$tag) {
                continue;
            }
            if ($tag->teamInfo) {
                $this->CM->cleanByEntity($tag->teamInfo->team);
            }
            $tagEntities[] = $tag;
        }

        /** @var Photo $photo */
        $photos = $this->PR->findByIds($photos);
        foreach ($photos as $photo) {
            $photo->removeAllTags();
            foreach ($tagEntities as $tag) {
                /** @var Tag $tag */
                $photo->addToTags($tag);
            }
            $this->PR->persist($photo);
        }

        $this->presenter->sendJson(['success' => 'true', 'tags' => array_map(function (Photo $p) {
            return ['id' => $p->id, 'tags' => count($p->tags)];
        }, array_values($photos))]);
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Utils\JsonException
     */
    public function handleInsertPhotos()
    {
        $data = Json::decode($this->request->getRawBody(), Json::FORCE_ARRAY);
        $photos = $data['photos'];

        /** @var Photo $photo */
        $photos = $this->PR->findByIds($photos);
        foreach ($photos as $photo) {
            $photo->active = TRUE;
            $this->PR->persist($photo);

        }

        $this->photos = array_filter($this->photos, function ($p) use ($data) {
            return !in_array($p, $data['photos']);
        });
        $this->session[$this->uploadId] = $this->photos;

        $this->presenter->sendJson(['success' => 'true']);
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Utils\JsonException
     * @throws \LeanMapper\Exception\InvalidStateException
     */
    public function handleDeletePhotos()
    {
        $data = Json::decode($this->request->getRawBody(), Json::FORCE_ARRAY);
        $photos = $data['photos'];

        /** @var Photo $photo */
        $photos = $this->PR->findByIds($photos);
        foreach ($photos as $photo) {
            $this->PM->delete($photo);
        }

        $this->photos = array_filter($this->photos, function ($p) use ($data) {
            return !in_array($p, $data['photos']);
        });
        $this->session[$this->uploadId] = $this->photos;

        $this->presenter->sendJson(['success' => 'true']);
    }
}
<?php

namespace Minicup\Components;


use Minicup\AdminModule\Presenters\BaseAdminPresenter;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Manager\CacheManager;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Minicup\Model\Repository\YearRepository;
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
    /** @var YearRepository */
    private $YR;
    private $autoDayTags = [
        '2019-05-31' => 'patek',
        '2019-06-01' => 'sobota',
        '2019-06-02' => 'nedele',
    ];

    /**
     * @param Session         $session
     * @param Request         $request
     * @param PhotoRepository $PR
     * @param TagRepository   $TR
     * @param PhotoManager    $PM
     * @param CacheManager    $CM
     * @param YearRepository  $YR
     */
    public function __construct(
        Session $session,
        Request $request,
        PhotoRepository $PR,
        TagRepository $TR,
        PhotoManager $PM,
        CacheManager $CM,
        YearRepository $YR)
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

        $this->YR = $YR;
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

        $photos = $this->PM->saveFromUpload($this->request->files['images'], $this->uploadId, $this->request->getPost('author'));
        foreach ($photos as $photo) {
            if (isset($this->autoDayTags[$photo->taken->format('Y-m-d')])) {
                $tag = $this->TR->getBySlug($this->autoDayTags[$photo->taken->format('Y-m-d')], $this->YR->getSelectedYear());
                $photo->addToTags($tag);
                $this->PR->persist($photo);
            }

        }
        $this->presenter->sendJson([
            'success' => true,
        ]);
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \LeanMapper\Exception\InvalidStateException
     */
    public function handleGetPhotos()
    {
        $photos = $this->PR->findByIds($this->photos); // TODO: not sure, what UX is better
        $photos = $this->PR->findUntaggedAndNotActivePhotos($this->YR->getSelectedYear());
        $this->presenter->sendJson([
            'photos' => array_values(array_map(function (Photo $p) {
                return [
                    'id' => $p->id,
                    'thumb' => $this->presenter->link(':Media:thumb', $p->filename),
                    'taken' => $p->taken ? $p->taken->getTimestamp() : null,
                    'tags' => array_map(function (Tag $t) {
                        bdump($t);
                        return $t->id;
                    }, $p->tags),
                ];
            }, $photos))
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
            $tagEntities[$tag->id] = $tag;
        }

        /** @var Photo $photo */
        $photos = $this->PR->findByIds($photos);
        foreach ($photos as $photo) {
            if (count($tagEntities) === 0)
                $photo->removeAllTags();

            foreach ($tagEntities as $tag) {
                /** @var Tag $tag */
                $photo->addToTags($tag);
            }
            $this->PR->persist($photo);
        }

        $this->presenter->sendJson(['success' => 'true']);
    }

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Utils\JsonException
     */
    public function handleInsertPhotos()
    {
        $data = Json::decode($this->request->getRawBody(), Json::FORCE_ARRAY);
        $photos = $data['photos'];
        bdump($photos);

        $tags = [];
        /** @var Photo $photo */
        $photoIds = array_map(function ($p) {
            return $p['id'];
        }, $photos);
        foreach ($photos as $item) {
            $photo = $this->PR->get($item['id']);
            foreach ($item['tags'] as $tagId) {
                if (!isset($tags[$tagId])) $tags[$tagId] = $this->TR->get($tagId);
                $photo->addToTags($tags[$tagId]);
            }
            $photo->active = TRUE;
            $this->PR->persist($photo);
        }

        $this->photos = array_filter($this->photos, function ($p) use ($photoIds) {
            return !in_array($p, $photoIds);
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
<?php

namespace Minicup\Components;


use Doctrine\DBAL\Migrations\Configuration\YamlConfiguration;
use Minicup\AdminModule\Presenters\BaseAdminPresenter;
use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Manager\CacheManager;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\UI\Multiplier;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Utils\Image;
use Nette\Utils\ImageException;
use Nette\Utils\Json;
use Nette\Utils\Random;

interface IPhotoTeamTaggerComponentFactory
{
    /** @return PhotoTeamTaggerComponent */
    public function create();
}

class PhotoTeamTaggerComponent extends BaseComponent
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
    /** @var TeamInfoRepository */
    private $TIR;

    /**
     * @param Session            $session
     * @param Request            $request
     * @param PhotoRepository    $PR
     * @param TagRepository      $TR
     * @param PhotoManager       $PM
     * @param CacheManager       $CM
     * @param YearRepository     $YR
     * @param TeamInfoRepository $TIR
     */
    public function __construct(Session $session, Request $request, PhotoRepository $PR,
                                TagRepository $TR, PhotoManager $PM, CacheManager $CM,
                                YearRepository $YR, TeamInfoRepository $TIR)
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
        $this->TIR = $TIR;
    }

    public function render()
    {
        $this->template->photos = $this->PR->findByIds($this->photos);
        $this->template->uploadId = $this->uploadId;
        $this->session[$this->uploadId] = $this->photos;
        parent::render();
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function handleGetPhotos()
    {
        bdump($this->presenter->getHttpRequest());
        $photos = $this->PR->findByIds($this->photos);
        $photos = $this->PR->findUntaggedAndNotActivePhotos($this->YR->getSelectedYear());
        $this->presenter->sendJson([
            'photos' => array_values(array_map(function (Photo $p) {
                return [
                    'id' => $p->id,
                    'thumb' => $this->presenter->link(':Media:thumb', $p->filename),
                    'tags' => array_map(function (Tag $t) {
                        return $t->id;
                    }, $p->tags),
                ];
            }, $photos))
        ]);
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function handleTeams()
    {
        if ($this->presenter->getHttpRequest()->isMethod(Request::POST)) {
            $teams = Json::decode($this->presenter->getHttpRequest()->getRawBody(), Json::FORCE_ARRAY);

            foreach ($teams['teams'] as $teamData) {
                /** @var TeamInfo $team */
                $team = $this->TIR->get($teamData['id']);
                $team->dressColorHistogram = Json::encode($teamData['color_histogram']);
                $this->TIR->persist($team);
            }
        } else {
            $this->presenter->sendJson([
                'teams' => array_values(array_map(function (TeamInfo $i) {
                    return [
                        'id' => $i->id,
                        'name' => "{$i->category->name} - {$i->name}",
                        'color_histogram' => $i->dressColorHistogram ? Json::decode($i->dressColorHistogram) : [],
                    ];
                }, $this->TIR->findInYear($this->YR->getSelectedYear())))
            ]);
        }
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
    public function handleAddTeamTag()
    {
        $data = Json::decode($this->request->getRawBody(), Json::FORCE_ARRAY);
        /** @var Photo $photo */
        $photo = $this->PR->get($data['photo']);
        /** @var TeamInfo $team */
        $team = $this->TIR->get($data['team']);

        $photo->addToTags($team->tag);
        $this->PR->persist($photo);
        bdump(array_map(function (Tag $t) {return $t->name;}, $photo->tags));

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
}
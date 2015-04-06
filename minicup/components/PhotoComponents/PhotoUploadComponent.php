<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Manager\PhotoManager;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;
use Nette\Utils\Strings;

// TODO: add forms to add tags with autocompleting thru ajax
class PhotoUploadComponent extends BaseComponent
{
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

    /** @var IPhotoEditComponentFactory */
    private $PECF;

    /** @var String */
    private $uploadId;

    /** @var int[] */
    private $photos = array();

    /**
     * @param string $wwwPath
     * @param Session $session
     * @param Request $request
     * @param PhotoRepository $PR
     * @param TagRepository $TR
     * @param PhotoManager $PM
     * @param IPhotoEditComponentFactory $PECF
     */
    public function __construct($wwwPath, Session $session, Request $request, PhotoRepository $PR, TagRepository $TR, PhotoManager $PM, IPhotoEditComponentFactory $PECF)
    {
        $this->request = $request;
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
        $this->photos = (array)$this->session[$this->uploadId];

    }

    public function render()
    {
        $this->template->photos = $this->PR->findByIds($this->photos);
        $this->template->uploadId = $this->uploadId;
        $this->session[$this->uploadId] = $this->photos;
        parent::render();
    }


    public function handleUpload()
    {
        $photos = $this->PM->save($this->request->files, $this->uploadId);
        foreach ($photos as $photo) {
            $this->photos[] = $photo->id;
        }
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
            $PEC->onDelete[] = function (Photo $photo) use ($PUC, $PR) {
                $PUC->photos = array_diff($PUC->photos, array($photo->id));
                $PUC->redrawControl('photos-list');
            };
            $PEC->onSave[] = function (Photo $photo) use ($PUC) {
                $PUC->photos = array_diff($PUC->photos, array($photo->id));
                $PUC->redrawControl('photos-list');
            };
            return $PEC;
        });
    }

    /**
     * Provide data about tags for select2 by optional term in post parameters
     *
     * @throws AbortException
     */
    public function handleTags()
    {
        $term = $this->request->getPost('term');
        if ($term) {
            $tags = $this->TR->findLikeTerm($term);
        } else {
            $tags = $this->TR->findAll();
        }
        $results = array();
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $results[] = array('id' => $tag->id, 'text' => $tag->name ? $tag->name : $tag->slug);
        }
        $this->presenter->sendJson(array('results' => $results));
    }

    /** Signal for tagging all actually uploaded photos */
    public function handleTagsAll()
    {
        $tags = $this->request->post['tags'];
        if (!$tags) {
            return;
        }
        $tags = $this->TR->findByIds($tags);
        $photos = $this->PR->findByIds($this->photos);
        foreach ($photos as $photo) {
            foreach ($tags as $tag) {
                if (!in_array($tag, $photo->tags)) {
                    $photo->addToTags($tag);
                }
            }
            $this->PR->persist($photo);
        }
        $this->redrawControl('photos-list');
    }

    /**
     * @return Form
     */
    public function createComponentAddTagForm()
    {
        $f = $this->formFactory->create();
        $f->addText('name')->setRequired();
        $f->addSubmit('submit', 'Přidat');
        $f->onSuccess[] = $this->addTagFormSuccess;
        return $f;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addTagFormSuccess(Form $form, ArrayHash $values)
    {
        $tag = new Tag();
        $tag->slug = Strings::webalize($values->name);
        $tag->name = $values->name;
        try {
            $this->TR->persist($tag);
        } catch (\DibiDriverException $e) {
            $this->presenter->flashMessage('Tento tag již existuje!', 'warning');
            return;
        }
        $form->setValues(array(), TRUE);
        $this->presenter->flashMessage('Tag přidán!', 'success');
    }
}

interface IPhotoUploadComponentFactory
{
    /** @return PhotoUploadComponent */
    public function create();
}
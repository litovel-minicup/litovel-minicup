<?php

namespace Minicup\Components;


use Minicup\Model\Entity\Photo;
use Minicup\Model\Entity\Tag;
use Minicup\Model\Repository\PhotoRepository;
use Minicup\Model\Repository\TagRepository;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\HiddenField;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

interface ITagFormComponentFactory
{
    /**
     * @param Tag $tag
     * @return TagFormComponent
     */
    public function create(Tag $tag = NULL);
}

class TagFormComponent extends BaseComponent
{
    /** @var TagRepository */
    private $TR;

    /** @var PhotoRepository */
    private $PR;

    /** @var Tag|NULL */
    private $tag;

    /**
     * @param Tag             $tag
     * @param TagRepository   $TR
     * @param PhotoRepository $PR
     */
    public function __construct(Tag $tag = NULL,
                                TagRepository $TR,
                                PhotoRepository $PR)
    {
        $this->TR = $TR;
        $this->tag = $tag;
        $this->PR = $PR;
        parent::__construct();
    }

    public function render()
    {
        $this->template->tag = $this->tag;
        if ($this->tag) {
            /** @var Form $form */
            $form = $this['tagForm'];
            $form->setDefaults($this->tag->getData(array('name', 'slug', 'id', 'is_main')));
            if ($this->tag->mainPhoto) {
                /** @var HiddenField $mainPhoto */
                $mainPhoto = $form['main_photo_id'];
                $mainPhoto->setValue($this->tag->mainPhoto->id);
            }
        }
        parent::render();
    }

    /**
     * @return Form
     */
    public function createComponentTagForm()
    {
        $f = $this->formFactory->create();
        $f->addText('name', 'Název');
        $f->addText('slug', 'Slug');
        $f->addCheckbox('is_main', 'Hlavní kategorie');
        $f->addHidden('main_photo_id');
        $f->addSubmit('submit', $this->tag ? 'Upravit' : 'Přidat');
        $f->onSuccess[] = $this->tagFormSuccess;
        return $f;
    }

    /**
     * @param Form      $form
     * @param ArrayHash $values
     */
    public function tagFormSuccess(Form $form, ArrayHash $values)
    {
        if ($this->tag) {
            $tag = $this->tag;
            $tag->slug = Strings::webalize($values->slug);
            $tag->name = $values->name;
            $tag->isMain = $values->is_main;
        } else {
            $tag = new Tag();
            $tag->name = $values->name;
            $tag->slug = Strings::webalize($values->name);
            $tag->isMain = $values->is_main;
        }

        if ($values->main_photo_id) {
            /** @var Photo $photo */
            $photo = $this->PR->get($values->main_photo_id);
            $tag->mainPhoto = $photo;
        }
        try {
            $this->TR->persist($tag);
        } catch (\DibiDriverException $e) {
            $this->presenter->flashMessage("Chyba při ukládání tagu {$tag->id} ({$tag->slug})!", 'warning');
            return;
        }
        $form->setValues(array(), TRUE);
        $this->presenter->flashMessage($this->tag ? 'Tag upraven!' : 'Tag přidán!', 'success');
        if ($this->presenter->action === 'tagDetail') {
            $this->presenter->redirect(':Admin:Photo:tags');
        } elseif ($this->presenter->isAjax()) {
            $this->redrawControl('tag-form');
        }
    }
}
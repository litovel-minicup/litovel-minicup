<?php

namespace Minicup\Components;


use Minicup\Model\Entity\News;
use Minicup\Model\Repository\NewsRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

class NewsFormComponent extends BaseComponent
{
    /** @var NewsRepository */
    private $NR;

    /** @var News */
    private $news;

    public function __construct(News $news = NULL, NewsRepository $NR)
    {
        $this->news = $news;
        $this->NR = $NR;
    }

    public function render()
    {
        $this->template->news = $this->news;
        if ($this->news) {
            /** @var Form $form */
            $form = $this['newsForm'];
            $form->setDefaults($this->news->getData(array('title', 'content', 'id')));
        }
        parent::render();
    }

    /**
     * @return Form
     */
    public function createComponentNewsForm()
    {
        $f = $this->formFactory->create();
        $f->addText("title", 'Titulek')->setRequired();
        $f->addHidden('id');
        $content = $f->addTextArea("content", 'Obsah')->setRequired();
        $content->getControlPrototype()->attrs["style"] = "width: 100%; max-width: 100%;";
        $rows = 10;
        if ($this->news) {
            $rows = count(Strings::match($this->news->content, '#\n#')) + 5;
        }
        $content->getControlPrototype()->attrs['rows'] = $rows;
        $f->addSubmit('submit', $this->news ? 'Upravit' : 'Přidat');
        $f->onSuccess[] = $this->newsFormSubmitted;
        return $f;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function newsFormSubmitted(Form $form, ArrayHash $values)
    {
        if ($this->news) {
            $news = $this->news;
        } else {
            $news = new News();
            $news->added = new \DibiDateTime();
        }
        $news->assign($values, array('title', 'content'));
        $news->updated = new \DibiDateTime();

        try {
            $this->NR->persist($news);
        } catch (\DibiDriverException $e) {
            $this->presenter->flashMessage("Chyba při ukládání novinky {$news->id}!", 'warning');
            return;
        }
        $form->setValues(array(), TRUE);
        $this->presenter->flashMessage($values->id ? "Novinka upravena!" : 'Novinka přidána!', 'success');
    }

}

interface INewsFormComponentFactory
{
    /**
     * @param News $news
     * @param NewsRepository $NR
     * @return NewsFormComponent
     */
    public function create(News $news = NULL);
}
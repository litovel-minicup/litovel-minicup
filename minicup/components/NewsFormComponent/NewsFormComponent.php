<?php

namespace Minicup\Components;


use Minicup\Model\Entity\News;
use Minicup\Model\Repository\NewsRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class NewsFormComponent extends BaseComponent
{
    /** @var NewsRepository */
    private $NR;

    /** @var News */
    private $news;

    public function __construct(News $news = NULL, NewsRepository $newsRepository)
    {
        $this->news = $news;
        $this->NR = $news;
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
        $f->addText("title")->setRequired();
        $f->addTextArea("content")->setRequired()->getControlPrototype()->attrs["style"] = "width: 100%; max-width: 100%;";
        $f->addSubmit('submit', $this->tag ? 'Upravit' : 'Přidat');
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
        $this->NR->persist($news);
        $this->presenter->redirect("this");
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
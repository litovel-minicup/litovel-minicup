<?php

namespace Minicup\Components;


use Minicup\Model\Entity\News;
use Minicup\Model\Repository\NewsRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class NewsListComponent extends BaseComponent
{
    /** @var NewsRepository */
    private $NR;

    /**
     * @param NewsRepository $NR
     */
    public function __construct(NewsRepository $NR)
    {
        $this->NR = $NR;
    }

    /**
     * @return Form
     */
    public function createComponentNewNewsForm()
    {
        $f = $this->formFactory->create();
        $f->addText("title")->setRequired();
        $f->addTextArea("content")->setRequired()->getControlPrototype()->attrs["style"] = "width: 100%; max-width: 100%;";
        $f->addSubmit("submit", "Přidat novinku");
        $f->onSuccess[] = $this->newNewsSubmitted;
        return $f;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function newNewsSubmitted(Form $form, ArrayHash $values)
    {
        $news = new News($values);
        $news->added = new \DibiDateTime();
        $this->NR->persist($news);
        $this->presenter->redirect("this");
    }

    public function render()
    {
        $this->template->news = $this->NR->findLastNews();
        parent::render();
    }
}

interface INewsListComponentFactory
{
    /**
     * @return NewsListComponent
     */
    public function create();

}
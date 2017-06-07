<?php

namespace Minicup\Components;


use Dibi\DateTime;
use Minicup\Misc\Texy;
use Minicup\Model\Entity\StaticContent;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\Year;
use Minicup\Model\Manager\StaticContentManager;
use Minicup\Model\Repository\StaticContentRepository;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

interface IStaticContentComponentFactory
{
    /**
     * @param StaticContent|string|Team $arg
     * @param Year                      $year
     * @param bool                      $showPlaceholder
     * @return StaticContentComponent
     */
    public function create($arg, Year $year, $showPlaceholder = TRUE);
}

class StaticContentComponent extends BaseComponent
{
    /** @var StaticContentRepository */
    private $SCR;

    /** @var Texy */
    private $texy;

    /** @var StaticContent */
    private $content;

    /** @var bool */
    private $showPlaceholder;

    /** @var callable[] */
    public $onChange = [];

    /**
     * @param                         $arg
     * @param Year                    $year
     * @param                         $showPlaceholder
     * @param StaticContentRepository $SCR
     * @param Texy                    $texy
     * @param StaticContentManager    $SCM
     */
    public function __construct($arg,
                                Year $year,
                                $showPlaceholder,
                                StaticContentRepository $SCR,
                                Texy $texy,
                                StaticContentManager $SCM)
    {
        parent::__construct();
        $this->showPlaceholder = $showPlaceholder;
        $this->SCR = $SCR;
        $this->texy = $texy;
        $this->content = $SCM->getContent($arg, $year);
    }

    public function render()
    {
        if (!isset($this->template->edit)) {
            $this->template->edit = FALSE;
        }
        $this->template->showPlaceholder = $this->showPlaceholder;
        $this->template->content = $this->texy->process($this->content->content);
        $this->template->staticContent = $this->content;
        parent::render();
    }

    public function hasContent()
    {
        return (bool)Strings::trim($this->content->content);
    }

    public function handleEdit()
    {
        $this->template->edit = TRUE;
        /** @var Form $form */
        $form = $this['editForm'];
        $form->setValues(['content' => $this->content->content]);
        if ($this->presenter->isAjax()) {
            $this->redrawControl('component');
        }
    }

    public function createComponentEditForm()
    {
        $f = $this->formFactory->create();
        $lines = count(Strings::matchAll($this->content->content, '#\n#'));
        $f->addTextArea('content', NULL, NULL, $lines + 10);
        $f->addSubmit('submit', 'Uložit');
        $f->addSubmit('cancel', 'Zrušit editaci');
        $f->onSuccess[] = [$this, 'editFormSuccess'];
        return $f;
    }

    public function editFormSuccess(Form $form, ArrayHash $values)
    {
        /** @var SubmitButton $submit */
        $submit = $form['submit'];
        if ($submit->isSubmittedBy()) {
            $this->content->content = $values->content;
            $this->content->updated = new DateTime();
            $this->SCR->persist($this->content);
            foreach ($this->onChange as $callback) {
                $callback($this->content);
            }
        }

        if ($this->presenter->isAjax()) {
            $this->redrawControl('component');
        } else {
            $this->presenter->redirect('this');
        }
    }


}
<?php

namespace Minicup\Components;

use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\EntityNotFoundException;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nextras\Forms\Rendering\Bs3FormRenderer;

class ListOfTeamsComponent extends BaseComponent
{
    /** @var  TeamRepository */
    private $TR;

    /** @var  TeamInfoRepository */
    private $TIR;

    /** @var  CategoryRepository */
    private $CR;

    public function __construct(TeamRepository $TR, TeamInfoRepository $TIR, CategoryRepository $CR)
    {
        parent::__construct();
        $this->TR = $TR;
        $this->TIR = $TIR;
        $this->CR = $CR;
    }

    public function render(Category $category)
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/ListOfTeamsComponent.latte');
        $template->category = $category;
        $template->teams = $category->teams;
        $template->render();
    }

    /**
     * @return Multiplier
     */
    protected function createComponentTeamEditForm()
    {
        $TR = $this->TR;
        return new Multiplier(function ($categoryId) use ($TR) {
            return new Multiplier(function ($teamId) use ($TR, $categoryId) {
                $f = new Form();
                $f->getElementPrototype()->class[] = 'form-inline';
                $f->setRenderer(new Bs3FormRenderer());
                $f->addText('name')->setRequired();
                $f->addText('slug')->setRequired();
                $f->addHidden('categoryId', $categoryId);
                $f->addSubmit('submit', $teamId ? 'Editovat' : 'Přidat');
                $f->onSubmit[] = $this->editFormSucceeded;
                try {
                    $team = $TR->get((int)$teamId);
                } catch (EntityNotFoundException $e) {
                    $team = NULL;
                }
                if ($team) {
                    $f['name']->setValue($team->i->name);
                    $f['slug']->setValue($team->i->slug);
                    $f->addHidden('teamId')->setValue($team->id);
                } else {
                    $f->addHidden('teamId')->setValue(0);
                }
                return $f;
            });
        });
    }

    public function editFormSucceeded(Form $form)
    {
        $category = $this->CR->get($form->values['categoryId']);
        $name = $form->values['name'];
        $slug = $form->values['slug'];
        $teamId = $form->values['teamId'];
        if ($teamId) {
            $teamInfo = $this->TR->get($teamId)->i;
        } else {
            $teamInfo = new TeamInfo();
        }
        $teamInfo->name = $name;
        $teamInfo->slug = $slug;
        $teamInfo->category = $category;
        $this->TIR->persist($teamInfo);
        if (!$teamId) {
            $team = new Team();
            $team->i = $teamInfo;
            $team->isActual = 1;
            $team->category = $category;
            $this->TR->persist($team);
        }
        if ($this->presenter->isAjax()) {
            $this->redrawControl();
        } else {
            $this->presenter->redirect('this');
        }

    }
}
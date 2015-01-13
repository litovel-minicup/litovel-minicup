<?php

namespace Minicup\Components;

use LeanMapper\Exception\InvalidValueException;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Team;
use Minicup\Model\Entity\TeamInfo;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\EntityNotFoundException;
use Minicup\Model\Repository\TeamInfoRepository;
use Minicup\Model\Repository\TeamRepository;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Utils\ArrayHash;
use Nextras\Forms\Rendering\Bs3FormRenderer;

class ListOfTeamsComponent extends BaseComponent
{
    /** @var  TeamRepository */
    private $TR;

    /** @var  TeamInfoRepository */
    private $TIR;

    /** @var  CategoryRepository */
    private $CR;

    /** @var  Category */
    private $category;

    public function __construct(Category $category, TeamRepository $TR, TeamInfoRepository $TIR, CategoryRepository $CR)
    {
        parent::__construct();
        $this->category = $category;
        $this->TR = $TR;
        $this->TIR = $TIR;
        $this->CR = $CR;
    }

    public function render()
    {
        $template = $this->template;
        $template->category = $this->category;
        $template->teams = $this->category->teams;
        $template->render();
    }

    /**
     * @return Multiplier
     */
    protected function createComponentTeamEditForm()
    {
        $TR = $this->TR;
        $FF = $this->FF;
        return new Multiplier(function ($categoryId) use ($TR, $FF) {
            return new Multiplier(function ($teamId) use ($TR, $FF, $categoryId) {
                $f = $FF->create();
                $f->getElementPrototype()->class[] = 'form-inline';
                $f->setRenderer(new Bs3FormRenderer());
                $f->addText('name')->setRequired();
                $f->addText('slug')->setRequired();
                $f->addHidden('categoryId', $categoryId);
                $f->addSubmit('submit', $teamId ? 'Editovat' : 'Přidat');
                $f->onSuccess[] = $this->editFormSucceeded;
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

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws EntityNotFoundException
     * @throws InvalidValueException
     */
    public function editFormSucceeded(Form $form, ArrayHash $values)
    {
        $category = $this->CR->get($values['categoryId']);
        $name = $values['name'];
        $slug = $values['slug'];
        $teamId = $values['teamId'];
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
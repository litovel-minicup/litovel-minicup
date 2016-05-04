<?php

namespace Minicup\AdminModule\Presenters;

use Grido\Components\Actions\Event;
use Grido\Components\Columns\Column;
use Grido\Components\Filters\Filter;
use Grido\Grid;
use Minicup\Presenters\BasePresenter;

abstract class BaseAdminPresenter extends BasePresenter
{
    public function startup()
    {
        parent::startup();
        if (!$this->user->loggedIn) {
            $this->flashMessage('Pro vstup do administrace je nutné se přihlásit.', 'error');
            $this->redirect(':Sign:in', array('backlink' => $this->storeRequest()));
        }
    }

    /**
     * before render
     */
    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->categories = $this->CR->findAll(FALSE);
        $this->template->years = $this->YR->findAll(FALSE);
    }


    protected function afterRender()
    {
        $this->redrawControl('flashes');
    }

    protected function createComponent($name)
    {
        $component = parent::createComponent($name);
        if ($component instanceof Grid) {
            return $this->improveGrid($component);
        }
        return $component;
    }

    /**
     * Ajaxed grid, added sorts to not custom rendered columns.
     *
     * @param Grid $grid
     * @return Grid
     * @throws \Exception
     */
    public function improveGrid(Grid $grid)
    {
        $grid->defaultPerPage = 100;
        $grid->setFilterRenderType(Filter::RENDER_INNER);
        $grid->customization->useTemplateBootstrap();
        $presenter = $this;
        foreach ($grid->getComponents(TRUE) as $child) {
            if ($child instanceof Event) {
                $child->getElementPrototype()->addAttributes(array('class' => 'ajax'));
                $child->setOnClick(function ($id) use ($grid, $presenter, $child) {
                    $presenter->flashMessage("Akce '{$child->getLabel()}' s prvkem {$id} byl úspěšně provedena!", 'success');
                    $grid->reload();
                });
            } elseif ($child instanceof Column) {
                if (!$child->getCustomRender() instanceof \Closure) {
                    $child->setSortable();
                }
            }
        }
        return $grid;
    }

}

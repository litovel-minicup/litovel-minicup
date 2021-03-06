<?php

namespace Minicup\Components;


use Grido\Grid;
use Minicup\AdminModule\Presenters\BaseAdminPresenter;
use Minicup\Misc\FilterLoader;
use Minicup\Misc\IFormFactory;
use Nette\Application\UI\Control;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Strings;

abstract class BaseComponent extends Control
{
    /** @var String|NULL */
    public $view;
    /** @var IFormFactory */
    protected $formFactory;
    /** @var FilterLoader */
    protected $filterLoader;
    /** @var bool */
    protected $productionMode;

    /**
     * @param IFormFactory $formFactory
     */
    public function injectFormFactory(IFormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param FilterLoader $filterLoader
     */
    public function injectFilterLoader(FilterLoader $filterLoader)
    {
        $this->filterLoader = $filterLoader;
    }

    /**
     * @param bool $productionMode
     */
    public function injectProductionMode($productionMode)
    {
        $this->productionMode = $productionMode;
    }

    /**
     * render component
     */
    public function render()
    {
        $this->template->productionMode = $this->productionMode;
        $this->template->render();
    }

    /**
     * @param string $name
     * @param array  $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        if ($name !== 'render' && substr($name, 0, 6) === 'render') {
            $this->tryCall($name, $args);
            $view = Strings::firstLower(Strings::substring($name, 6));
            $this->view = $view;
            return call_user_func_array([$this, 'render'], $args);
        } else {
            return parent::__call($name, $args);
        }
    }

    /**
     * refresh entire component snippet
     */
    public function handleRefresh()
    {
        $this->redrawControl();
    }

    /**
     * adding views
     *
     * @return \Nette\Application\UI\ITemplate
     */
    protected function createTemplate()
    {
        $template = $this->filterLoader->loadFilters(parent::createTemplate());
        $name = static::getReflection()->getShortName();
        $dir = $this->presenter->context->parameters['appDir'];
        $paths = [];
        if ($this->view) {
            $view = $this->view;
            $paths[] = "$dir/templates/components/$name/$view.latte";
            $paths[] = "$dir/templates/components/$name.$view.latte";
        } else {
            $paths[] = "$dir/templates/components/$name/default.latte";
            $paths[] = "$dir/templates/components/$name.latte";
        }
        foreach ($paths as $path) {
            if (is_file($path)) {
                $template->setFile($path);
                return $template;
            }
        }
        $template->setFile($paths[0]);
        return $template;
    }

    /**
     * @param string $name
     * @return IComponent
     */
    protected function createComponent($name)
    {
        $comp = parent::createComponent($name);
        if ($comp instanceof Grid && $this->getPresenter(FALSE) && $this->getPresenter() instanceof BaseAdminPresenter) {
            return $this->getPresenter()->improveGrid($comp);
        }
        return $comp;
    }


}
<?php

namespace Minicup\Components;


use Minicup\Misc\FilterLoader;
use Minicup\Misc\IFormFactory;
use Nette\Application\UI\Control;
use Nette\Utils\Strings;

abstract class BaseComponent extends Control
{
    /** @var IFormFactory */
    protected $formFactory;

    /** @var FilterLoader */
    protected $filterLoader;

    /** @var bool */
    protected $productionMode;

    /** @var String|NULL */
    public $view = NULL;

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
     * adding views
     * @return \Nette\Application\UI\ITemplate
     */
    protected function createTemplate()
    {
        $template = $this->filterLoader->loadFilters(parent::createTemplate());
        $name = $this->reflection->shortName;
        $dir = $this->presenter->context->parameters['appDir'];
        $paths = array();
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
     * render component
     */
    public function render()
    {
        $this->template->productionMode = $this->productionMode;
        $this->template->render();
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (substr($name, 0, 6) == "render" && $name !== "render") {
            $this->tryCall($name, $args);
            $view = Strings::lower(Strings::substring($name, 6));
            $this->view = $view;
            return call_user_func_array($this->render, $args);
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
}
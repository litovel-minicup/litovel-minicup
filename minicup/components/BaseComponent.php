<?php

namespace Minicup\Components;


use Minicup\Misc\IFormFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;

abstract class BaseComponent extends Control
{
    /** @var  IFormFactory */
    protected $FF;

    /** @var String|NULL */
    protected $view = NULL;

    /**
     * @param IFormFactory $FF
     */
    public function injectFF(IFormFactory $FF)
    {
        $this->FF = $FF;
    }

    /**
     * @param $presenter
     */
    protected function attached($presenter)
    {
        if ($presenter instanceof Presenter) {
            // TODO: exists better solution for this problem?
            $presenter->context->callInjects($this);
        }
        parent::attached($presenter);
    }

    /**
     * adding views
     * @return \Nette\Application\UI\ITemplate
     */
    protected function createTemplate()
    {
        $template = parent::createTemplate();
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
            return $this->render();
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
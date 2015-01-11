<?php

namespace Minicup\Components;


use Minicup\Forms\IFormFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;

class BaseComponent extends Control
{
    /** @var  IFormFactory */
    protected $FF;

    /** @var String|null  */
    protected $view = NULL;

    /**
     * @param IFormFactory $FF
     */
    public function injectFF(IFormFactory $FF)
    {
        $this->FF = $FF;
    }

    protected function attached($presenter)
    {
        if ($presenter instanceof Presenter) {
            // TODO: exists better solution for this problem?
            $presenter->context->callInjects($this);
        }
        parent::attached($presenter);
    }

    protected function createTemplate()
    {
        $template = parent::createTemplate();
        $name = $this->reflection->shortName;
        if ($this->view) {
            $name .= '.'.$this->view;
        }
        $dir = $this->presenter->context->parameters['appDir'];
        $template->setFile("$dir/templates/components/$name.latte");
        return $template;
    }


}
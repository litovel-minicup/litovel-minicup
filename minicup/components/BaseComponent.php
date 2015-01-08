<?php

namespace Minicup\Components;


use Minicup\Forms\IFormFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;

class BaseComponent extends Control
{
    /** @var  IFormFactory */
    protected $FF;

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


}
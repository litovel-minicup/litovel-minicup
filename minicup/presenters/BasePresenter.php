<?php

namespace Minicup\Presenters;

use Minicup\Components\ILoginFormComponentFactory;
use Minicup\Forms\IFormFactory;
use Minicup\Model;
use Nette;
use Tracy\Debugger;

/**
 * Base presenter.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var ILoginFormComponentFactory @inject */
    public $LFCF;

    /** @var  IFormFactory @inject */
    public $FF;

    /** @var Model\Repository\CategoryRepository @inject */
    public $CR;

    /** @var Model\Repository\YearRepository @inject */
    public $YR;

    /**
     * @return Nette\Application\UI\Form
     */
    protected function createComponentLoginForm()
    {
        return $this->LFCF->create();
    }

    /**
     * before render
     */
    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->categories = $this->CR->findAll();
    }

    /**
     * Formats layout template file names.
     * @return array
     */
    public function formatLayoutTemplateFiles()
    {
        $layout = $this->layout ? $this->layout : 'layout';
        $dir = $this->context->parameters['appDir'];
        $names = Nette\Utils\Strings::split($this->getName(), '(:)');
        $module = $names[0];
        $presenter = $names[1];
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        $list = [
            "$dir/templates/$module/$presenter/@$layout.latte",
            "$dir/templates/$module/$presenter.@$layout.latte",
            "$dir/templates/$module.$presenter.@$layout.latte",
            "$dir/templates/$module/@$layout.latte",
            "$dir/templates/$module.@$layout.latte",
        ];
        do {
            $list[] = "$dir/templates/@$layout.latte";
            $dir = dirname($dir);
        } while ($dir && ($name = substr($presenter, 0, strrpos($presenter, ':'))));
        return $list;
    }


    /**
     * Formats view template file names.
     * @return array
     */
    public function formatTemplateFiles()
    {
        $dir = $this->context->parameters['appDir'];
        $names = Nette\Utils\Strings::split($this->getName(), '(:)');
        $module = $names[0];
        $presenter = $names[1];
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        $list = [
            "$dir/templates/$module.$presenter.$this->view.latte",
            "$dir/templates/$module/$presenter.$this->view.latte",
            "$dir/templates/$module/$presenter/$this->view.latte",
        ];
        return $list;
    }


}

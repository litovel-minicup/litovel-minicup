<?php

namespace Minicup\Presenters;

use Minicup\Components\CssComponentFactory;
use Minicup\Components\JsComponentFactory;
use Minicup\Misc\FilterLoader;
use Minicup\Misc\IFormFactory;
use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Year;
use Minicup\Model\Manager\CacheManager;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;
use Tracy\Debugger;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;

/**
 * Base presenter.
 */
abstract class BasePresenter extends Presenter {
    /** @var IFormFactory @inject */
    public $formFactory;

    /** @var FilterLoader @inject */
    public $filterLoader;

    /** @var CategoryRepository @inject */
    public $CR;

    /** @var YearRepository @inject */
    public $YR;

    /** @var CssComponentFactory @inject */
    public $CSSCF;

    /** @var JsComponentFactory @inject */
    public $JSCF;

    /** @var CacheManager @inject */
    public $CM;

    /** @var Category @persistent */
    public $category;

    /** @var string */
    protected $module;

    /**
     * before render
     */
    public function beforeRender() {
        parent::beforeRender();
        $this->template->absoluteUrl = $this->getHttpRequest()->getUrl()->absoluteUrl;
        $this->template->productionMode = $this->context->parameters["productionMode"];
    }

    /**
     * Formats layout template file names.
     *
     * @return array
     */
    public function formatLayoutTemplateFiles() {
        $layout = $this->layout ? $this->layout : 'layout';
        $dir = $this->context->parameters['appDir'];
        $names = Strings::split($this->getName(), '(:)');
        $module = $names[0];
        $presenter = $names[1];
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        $list = array(
            "$dir/templates/$module/$presenter/@$layout.latte",
            "$dir/templates/$module/$presenter.@$layout.latte",
            "$dir/templates/$module.$presenter.@$layout.latte",
            "$dir/templates/$module/@$layout.latte",
            "$dir/templates/$module.@$layout.latte",
        );
        do {
            $list[] = "$dir/templates/@$layout.latte";
            $dir = dirname($dir);
        } while ($dir && ($name = substr($presenter, 0, strrpos($presenter, ':'))));
        return $list;
    }

    /**
     * Formats view template file names.
     *
     * @return array
     */
    public function formatTemplateFiles() {
        $dir = $this->context->parameters['appDir'];
        $names = Strings::split($this->getName(), '(:)');
        $module = $names[0];
        $presenter = $names[1];
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        $list = array(
            "$dir/templates/$module.$presenter.$this->view.latte",
            "$dir/templates/$module/$presenter.$this->view.latte",
            "$dir/templates/$module/$presenter/$this->view.latte",
        );
        return $list;
    }

    /**
     * Loads base filters from filter loader.
     *
     * @return ITemplate
     */
    public function createTemplate() {
        return $this->filterLoader->loadFilters(parent::createTemplate());
    }

    /**
     * set module property
     */
    protected function startup() {
        parent::startup();
        // $this->CM->initEvents();
        $splitName = Strings::split($this->getName(), '(:)');
        $this->module = Strings::lower($splitName[0]);
    }

    /** @return CssLoader */
    protected function createComponentCss() {
        return $this->CSSCF->create($this->module);
    }

    /** @return JavaScriptLoader */
    protected function createComponentJs() {
        return $this->JSCF->create($this->module);
    }
}

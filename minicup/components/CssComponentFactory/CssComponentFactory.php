<?php

namespace Minicup\Components;


use Nette\Http\IRequest;
use Nette\Object;
use Nette\Utils\Strings;
use WebLoader\Compiler;
use WebLoader\FileCollection;
use WebLoader\InvalidArgumentException;
use WebLoader\Nette\CssLoader;

/**
 * Factory for generating css component
 * @package Minicup\Components
 */
class CssComponentFactory extends Object
{
    /** @var IRequest */
    public $request;
    /** @var  string */
    private $wwwPath;
    /** @var  bool */
    private $productionMode;

    /**
     * @param string   $wwwPath
     * @param bool     $productionMode
     * @param IRequest $request
     */
    public function __construct($wwwPath,
                                $productionMode,
                                IRequest $request)
    {
        $this->wwwPath = $wwwPath;
        $this->productionMode = $productionMode;
        $this->request = $request;
    }

    /**
     * @param string $module
     * @return CssLoader
     * @throws InvalidArgumentException
     */
    public function create($module)
    {
        $files = new FileCollection($this->wwwPath);
        $control = $this;
        $files->addFile('assets/css/select2.css');
        $files->addFile('assets/css/swipebox.css');
        if ($module === 'front') {
            $files->addFile('assets/css/reset.css');
            $files->addFile('assets/css/index.css');
        } elseif ($module === 'admin') {
            $files->addFile('assets/css/admin/jquery.fs.dropper.css');
            $files->addFile('assets/css/admin/index.css');
            $files->addFile('assets/css/admin/bootstrap.css');
            $files->addFile('assets/css/admin/toastr.css');
        }
        $compiler = Compiler::createCssCompiler($files, $this->wwwPath . '/webtemp');
        // TODO: add urls fixing
        // $compiler->addFileFilter(new CssUrlFilter("assets/", $this->request));

        // TODO: Errrghh!!!
        $compiler->addFileFilter(function ($code, Compiler $loader, $file = null) use ($control) {
            return Strings::replace($code, "#\.\./#", $control->request->getUrl()->scriptPath . 'assets/');
        });

        if ($this->productionMode) {
            $compiler->addFilter(function ($code) {
                return \CssMin::minify($code);
            });
        }
        $control = new CssLoader($compiler, $this->request->getUrl()->scriptPath . 'webtemp');
        return $control;
    }
}
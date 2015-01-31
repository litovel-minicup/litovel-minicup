<?php

namespace Minicup\Components;


use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Object;
use Nette\Utils\Finder;
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
    /** @var  string */
    private $wwwPath;

    /** @var  bool */
    private $productionMode;

    /** @var IRequest */
    private $request;

    /**
     * @param string $wwwPath
     * @param bool $productionMode
     * @param Request $request
     */
    public function __construct($wwwPath, $productionMode,  IRequest $request)
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

        if ($module === 'front') {
            $files->addFile('assets/css/index.css');
            $files->addFiles(Finder::findFiles('*.css')->in(($this->wwwPath . '/assets/css')));
            $files->addRemoteFile('cdn.jsdelivr.net/chartist.js/latest/chartist.min.css');
        } elseif ($module === 'admin') {
            $files->addRemoteFile('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css');
        }
        $compiler = Compiler::createCssCompiler($files, $this->wwwPath . '/webtemp');
        // TODO: add urls fixing
        //$compiler->addFilter(new CssUrlsFilter());

        if ($this->productionMode) {
            $compiler->addFilter(function ($code) {
                return \CssMin::minify($code);
            });
        }
        $control = new CssLoader($compiler, $this->request->getUrl()->scriptPath . 'webtemp');
        return $control;
    }
}
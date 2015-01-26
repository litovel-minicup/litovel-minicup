<?php

namespace Minicup\Components;


use Nette\Object;
use Nette\Utils\Finder;
use WebLoader\Compiler;
use WebLoader\FileCollection;
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


    /**
     * @param string $wwwPath
     * @param bool $productionMode
     */
    public function __construct($wwwPath, $productionMode)
    {
        $this->wwwPath = $wwwPath;
        $this->productionMode = $productionMode;
    }

    /**
     * @return CssLoader
     */
    public function create()
    {
        $files = new FileCollection($this->wwwPath);
        $files->addFile('assets/scss/index.css');

        $files->addFiles(Finder::findFiles('*.css')->from($this->wwwPath.'/assets/css'));

        $files->addRemoteFile('//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css');

        $compiler = Compiler::createCssCompiler($files, $this->wwwPath.'/temp');
        // TODO: add urls fixing
        //$compiler->addFilter(new CssUrlsFilter());

        if ($this->productionMode) {
            $compiler->addFilter(function ($code) {
                return \CssMin::minify($code);
            });
        }
        $control = new CssLoader($compiler, '/temp');
        return $control;
    }
}
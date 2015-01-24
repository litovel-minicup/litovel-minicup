<?php

namespace Minicup\Components;


use Nette\Object;
use WebLoader\Compiler;
use WebLoader\FileCollection;
use WebLoader\Filter\ScssFilter;
use WebLoader\Nette\CssLoader;

class CssComponentFactory extends Object
{
    /** @var  string */
    private $wwwPath;

    /**
     * @param string $wwwPath
     * @param ScssFilter $scssFilter
     */
    public function __construct($wwwPath)
    {
        $this->wwwPath = $wwwPath;
    }

    /**
     * @return CssLoader
     */
    public function create()
    {
        $files = new FileCollection($this->wwwPath);
        $files->addFile('scss/index.css');
        $files->addFile('css/grido.css');

        $compiler = Compiler::createCssCompiler($files, $this->wwwPath . '/temp');

        // TODO: add urls fixing
        //$compiler->addFilter(new CssUrlsFilter());

        $control = new CssLoader($compiler, '/temp');
        return $control;
    }
}
<?php

namespace Minicup\Components;


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
     * @param string $module
     * @return CssLoader
     * @throws InvalidArgumentException
     */
    public function create($module)
    {
        $files = new FileCollection($this->wwwPath);

        if ($module === 'front') {
            $files->addFile('assets/scss/index.css');
            $files->addFiles(Finder::findFiles('*.css')->in(($this->wwwPath . '/assets/css')));
            $files->addRemoteFile('cdn.jsdelivr.net/chartist.js/latest/chartist.min.css');
        } elseif ($module === 'admin') {
            $files->addRemoteFile('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css');
        }
        $compiler = Compiler::createCssCompiler($files, $this->wwwPath . '/temp');
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
<?php

namespace Minicup\Components;


use Nette\Object;
use Nette\Utils\Finder;
use WebLoader\Compiler;
use WebLoader\FileCollection;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;

/**
 * Factory for generating js component
 * @package Minicup\Components
 */
class JsComponentFactory extends Object
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
        $files->addRemoteFile('http://code.jquery.com/jquery-2.1.1.min.js');
        $files->addFile('assets/js/grido.js');
        $files->addFiles(Finder::findFiles('*.js')->from($this->wwwPath.'/assets/js'));
        $files->addFile('assets/js/main.js');

        $compiler = Compiler::createJsCompiler($files, $this->wwwPath.'/temp');

        $control = new JavaScriptLoader($compiler, '/temp');
        return $control;
    }
}
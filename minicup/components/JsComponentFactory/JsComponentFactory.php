<?php

namespace Minicup\Components;


use Closure\RemoteCompiler;
use Nette\Object;
use Nette\Utils\Finder;
use WebLoader\Compiler;
use WebLoader\FileCollection;
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
     * @return JavaScriptLoader
     */
    public function create($module)
    {
        $files = new FileCollection($this->wwwPath);
        $files->addRemoteFile('http://code.jquery.com/jquery-2.1.1.min.js');

        if ($module === 'front') {
            $files->addRemoteFile('cdn.jsdelivr.net/chartist.js/latest/chartist.min.js');
            $files->addFiles(Finder::findFiles('*.js')->in($this->wwwPath . '/assets/js'));
            $files->addFile('assets/js/main.js');

        } elseif($module === 'admin') {
            $files->addFile('assets/js/admin/grido.js');
            $files->addFile('assets/js/admin/grido.ext.js');
            $files->addRemoteFile('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js');
        }

        $compiler = Compiler::createJsCompiler($files, $this->wwwPath . '/temp');

        if ($this->productionMode) {
            $compiler->addFilter(function ($code) {
                $remoteCompiler = new RemoteCompiler();
                $remoteCompiler->addScript($code);
                $remoteCompiler->setMode(RemoteCompiler::MODE_SIMPLE_OPTIMIZATIONS);
                return $remoteCompiler->compile()->getCompiledCode();
            });
        }
        $control = new JavaScriptLoader($compiler, '/temp');
        return $control;
    }
}
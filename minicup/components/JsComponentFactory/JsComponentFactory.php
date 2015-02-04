<?php

namespace Minicup\Components;


use Closure\RemoteCompiler;
use Nette\Http\IRequest;
use Nette\Object;
use Nette\Utils\Finder;
use WebLoader\Compiler;
use WebLoader\FileCollection;
use WebLoader\InvalidArgumentException;
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

    /** @var IRequest */
    private $request;

    /**
     * @param string $wwwPath
     * @param string $productionMode
     * @param IRequest $request
     */
    public function __construct($wwwPath, $productionMode, IRequest $request)
    {
        $this->wwwPath = $wwwPath;
        $this->productionMode = $productionMode;
        $this->request = $request;
    }

    /**
     * @param string $module
     * @return JavaScriptLoader
     * @throws InvalidArgumentException
     */
    public function create($module)
    {
        $files = new FileCollection($this->wwwPath);
        $files->addRemoteFile('http://code.jquery.com/jquery-2.1.1.min.js');
        $files->addFile($this->wwwPath.'/assets/js/nette.ajax.js');

        if ($module === 'front') {
            $files->addRemoteFile('//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js');
            $files->addFiles(Finder::findFiles('*.js')->in($this->wwwPath . '/assets/js'));
            $files->addFile('assets/js/main.js');

        } elseif ($module === 'admin') {
            $files->addFile('assets/js/admin/grido.js');
            $files->addFile('assets/js/admin/grido.ext.js');
            $files->addRemoteFile('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js');
        }

        $compiler = Compiler::createJsCompiler($files, $this->wwwPath . '/webtemp');

        if ($this->productionMode) {
            $compiler->addFilter(function ($code) {
                $remoteCompiler = new RemoteCompiler();
                $remoteCompiler->addScript($code);
                $remoteCompiler->setMode(RemoteCompiler::MODE_SIMPLE_OPTIMIZATIONS);
                return $remoteCompiler->compile()->getCompiledCode();
            });
        }
        $control = new JavaScriptLoader($compiler, $this->request->getUrl()->scriptPath . 'webtemp');
        return $control;
    }
}
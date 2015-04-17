<?php

namespace Minicup\Components;


use Closure\RemoteCompiler;
use Nette\Http\IRequest;
use Nette\Object;
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
        $files->addFile('assets/js/jquery.js');
        $files->addFile('assets/js/select2.js');
        $files->addFile('assets/js/nette.ajax.js');
        $files->addFile('assets/js/nette.forms.js');
        $files->addFile('assets/js/dropper.js');
        $files->addFile('assets/js/main.js');

        if ($module === 'front') {
            $files->addFile('assets/js/chartist.js');
        } elseif ($module === 'admin') {
            $files->addFile('assets/js/admin/grido.js');
            $files->addFile('assets/js/admin/grido.ext.js');
            $files->addFile('assets/js/admin/main.js');
            $files->addFile('assets/js/bootstrap.min.js');
            $files->addFile('assets/js/toastr.min.js');
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
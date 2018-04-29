<?php

namespace Minicup\Components;


use JShrink\Minifier;
use Nette\Http\IRequest;

use Nette\SmartObject;
use WebLoader\Compiler;
use WebLoader\FileCollection;
use WebLoader\InvalidArgumentException;
use WebLoader\Nette\Diagnostics\Panel;
use WebLoader\Nette\JavaScriptLoader;

/**
 * Factory for generating js component
 * @package Minicup\Components
 */
class JsComponentFactory
{

    use SmartObject;
    /** @var  string */
    private $wwwPath;

    /** @var  bool */
    private $productionMode;

    /** @var IRequest */
    private $request;

    /**
     * @param string   $wwwPath
     * @param string   $productionMode
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
     * @return JavaScriptLoader
     * @throws InvalidArgumentException
     */
    public function create($module)
    {
        $files = new FileCollection($this->wwwPath);
        $files->addFile('assets/js/jquery.js');
        $files->addFile('assets/js/jquery.plugin.js');
        $files->addFile('assets/js/dropper.js');
        $files->addFile('assets/js/select2.js');
        $files->addFile('assets/js/nette.ajax.js');
        $files->addFile('assets/js/nette.ajax.confirm.js');
        $files->addFile('assets/js/nette.forms.js');
        $files->addFile('assets/js/jquery.swipebox.js');
        $files->addFile('assets/js/bootstrap.js');
        $files->addFile('assets/js/main.js');

        if ($module === 'front') {
            $files->addFile('assets/js/chartist.js');
            $files->addFile('assets/js/chartist.tooltip.js');
            $files->addFile('assets/js/chartist.legend.js');
            $files->addFile('assets/js/chartist.barlabels.js');
            $files->addFile('assets/js/jquery.countdown.js');
            $files->addFile('assets/js/jquery.countdown.cs.js');
        } elseif ($module === 'admin') {
            $files->addFile('assets/js/admin/grido.js');
            $files->addFile('assets/js/admin/grido.ext.js');
            $files->addFile('assets/js/admin/toastr.js');
            $files->addFile('assets/js/admin/main.js');
        }

        $compiler = Compiler::createJsCompiler($files, $this->wwwPath . '/webtemp');

        if ($this->productionMode) {
            $compiler->addFilter(function ($code) {
                return Minifier::minify($code, ['flaggedComments' => false]);
            });
        }
        $control = new JavaScriptLoader($compiler, $this->request->getUrl()->scriptPath . 'webtemp');
        return $control;
    }
}
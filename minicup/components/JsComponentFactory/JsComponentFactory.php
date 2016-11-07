<?php

namespace Minicup\Components;


use JShrink\Minifier;
use Nette\Http\IRequest;
use Nette\Object;
use WebLoader\Compiler;
use WebLoader\FileCollection;
use WebLoader\InvalidArgumentException;
use WebLoader\Nette\Diagnostics\Panel;
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
     * @var Panel
     */
    private $tracyPanel;

    /**
     * @param string   $wwwPath
     * @param string   $productionMode
     * @param IRequest $request
     * @param Panel    $panel
     */
    public function __construct($wwwPath,
                                $productionMode,
                                IRequest $request,
                                Panel $panel)
    {
        $this->wwwPath = $wwwPath;
        $this->productionMode = $productionMode;
        $this->request = $request;
        $this->tracyPanel = $panel;
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

        $this->tracyPanel->addLoader('js', $compiler);
        $control = new JavaScriptLoader($compiler, $this->request->getUrl()->scriptPath . 'webtemp');
        return $control;
    }
}
<?php

namespace Minicup\Components;


use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Object;
use Nette\Utils\Strings;
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
    public function __construct($wwwPath, $productionMode, IRequest $request)
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
        $control = $this;
        $files->addRemoteFile('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-beta.3/css/select2.min.css');
        if ($module === 'front') {
            $files->addRemoteFile('//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css');
            $files->addFile('assets/css/reset.css');
            $files->addFile('assets/css/index.css');
        } elseif ($module === 'admin') {
            $files->addFile('assets/css/admin/jquery.fs.dropper.css');
            $files->addFile('assets/css/admin/index.css');
            $files->addRemoteFile('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css');
            $files->addRemoteFile('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css');
        }
        $compiler = Compiler::createCssCompiler($files, $this->wwwPath . '/webtemp');
        // TODO: add urls fixing
        // $compiler->addFileFilter(new CssUrlFilter("assets/", $this->request));

        // TODO: Errrghh!!!
        $compiler->addFileFilter(function ($code, Compiler $loader, $file = null) use ($control) {
            return Strings::replace($code, "#\.\./#", $control->request->url->scriptPath . "assets/");
        });

        if ($this->productionMode) {
            $compiler->addFilter(function ($code) {
                return \CssMin::minify($code);
            });
        }
        $control = new CssLoader($compiler, $this->request->url->scriptPath . 'webtemp');
        return $control;
    }
}
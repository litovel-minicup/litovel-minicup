<?php

namespace Minicup\Components;


use Nette\Http\Request;

class PhotoUploadComponent extends BaseComponent
{
    /** @var Request */
    private $httpRequest;

    /**
     * @param Request $httpRequest
     */
    public function __construct(Request $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    public function render()
    {
        if (!isset($this->template->files)) {
            $this->template->files = $this->httpRequest->files;
        }

        parent::render();
    }

    public function handleUpload()
    {
        $this->template->files = $this->httpRequest->files;
        $this->redrawControl('files');
    }
}
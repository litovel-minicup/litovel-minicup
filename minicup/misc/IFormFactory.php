<?php

namespace Minicup\Misc;


use Nette\Application\UI\Form;

interface IFormFactory
{
    /**
     * @return Form
     */
    public function create();
}
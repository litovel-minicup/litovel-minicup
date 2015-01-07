<?php

namespace Minicup\Forms;


use Nette\Application\UI\Form;

interface IFormFactory
{
    /**
     * @return Form
     */
    public function create();
}
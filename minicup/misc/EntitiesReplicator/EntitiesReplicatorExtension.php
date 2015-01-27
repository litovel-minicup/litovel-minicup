<?php

namespace Minicup\Misc;


use Nette\DI\CompilerExtension;
use Nette\PhpGenerator as Code;

class EntitiesReplicatorExtension extends CompilerExtension
{
    /**
     * @param Code\ClassType $class
     */
    public function afterCompile(Code\ClassType $class)
    {
        $init = $class->methods['initialize'];
        $init->addBody('Minicup\Misc\EntitiesReplicatorContainer::register();');
    }
}
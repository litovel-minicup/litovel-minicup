<?php
namespace Minicup\Misc;


use Grido\Components\Columns\Column;
use Minicup\Model\Repository\BaseRepository;
use Nette\StaticClass;

class GridHelpers
{
    use StaticClass;

    /**
     * @param             string $attr
     * @param BaseRepository     $repository
     * @return \Closure
     */
    public static function getEditableCallback($attr, BaseRepository $repository)
    {
        return function ($id, $newValue, $oldValue, Column $column) use ($attr, $repository) {
            $entity = $repository->get($id, FALSE);
            $entity->{$attr} = $newValue;
            $repository->persist($entity);
            return TRUE;
        };
    }

}
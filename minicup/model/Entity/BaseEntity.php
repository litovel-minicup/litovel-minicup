<?php

namespace Minicup\Model\Entity;


use LeanMapper\Entity;

abstract class BaseEntity extends Entity
{
    const CACHE_GLUE = '_';

    public static $CACHE_TAG = 'entity';

    /**
     * @return string
     */
    public function getCacheTag()
    {
        return $this::$CACHE_TAG . $this::CACHE_GLUE . $this->id;
    }

    public function cleanCache()
    {
        $this->row->cleanReferencedRowsCache();
        $this->row->cleanReferencingRowsCache();
    }
}
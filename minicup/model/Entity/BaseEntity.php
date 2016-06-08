<?php

namespace Minicup\Model\Entity;


use LeanMapper\Entity;

abstract class BaseEntity extends Entity
{
    const CACHE_GLUE = '_';

    public static $CACHE_TAG = 'entity';

    /**
     * @param string $postfix
     * @return string
     */
    public function getCacheTag($postfix = '')
    {
        return $this::$CACHE_TAG . $this::CACHE_GLUE . $this->id . ($postfix ? $this::CACHE_GLUE . $postfix : '');
    }

    public function cleanCache()
    {
        $this->row->cleanReferencedRowsCache();
        $this->row->cleanReferencingRowsCache();
    }
}
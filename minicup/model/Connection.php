<?php

namespace Minicup\Model;


class Connection extends \LeanMapper\Connection
{
    /**
     * @param callable $func
     * @param NULL     $savepoint
     * @throws \Dibi\Exception
     * @throws \Exception
     */
    public function transactional(callable $func, $savepoint = NULL)
    {
        $this->begin($savepoint);
        try {
            $func($this);
            $this->commit($savepoint);
        } catch (\Exception $e) {
            $this->rollback($savepoint);
            throw $e;
        }

    }
}
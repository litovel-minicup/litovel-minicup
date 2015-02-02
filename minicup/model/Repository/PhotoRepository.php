<?php

namespace Minicup\Model\Repository;


use LeanMapper\Connection;
use LeanMapper\Events;
use LeanMapper\IEntityFactory;
use LeanMapper\IMapper;

class PhotoRepository extends BaseRepository
{
    public function __construct(Connection $connection, IMapper $mapper, IEntityFactory $entityFactory)
    {
        parent::__construct($connection, $mapper, $entityFactory);
        $this->events->registerCallback(Events::EVENT_AFTER_PERSIST, function () {
            //TODO: saving image to FS
        });
    }

}
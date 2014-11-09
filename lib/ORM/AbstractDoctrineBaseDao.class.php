<?php

namespace Panda\ORM;

use Panda\Core\Component\Bundle\Dao\AbstractBasicDao;

class AbstractDoctrineBaseDao extends AbstractBasicDao
{
    protected static $entityManagers = array();

    public function getEntityManager($entityManagerName)
    {

    }
} 
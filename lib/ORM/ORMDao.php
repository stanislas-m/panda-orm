<?php
/**
 * Created by IntelliJ IDEA.
 * User: stan
 * Date: 10/11/14
 * Time: 19:47
 */

namespace Panda\ORM;

interface ORMDao
{
    public function getEntityManager($entityManagerName);
} 
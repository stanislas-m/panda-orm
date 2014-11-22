<?php

namespace Panda\ORM;

interface ORMDao
{
    public function getEntityManager($datasourceName);

    public function newEntity($entityName, array $args);

    public function editEntity(Entity $entity, array $args = array());
} 
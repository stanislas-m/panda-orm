<?php

namespace Panda\ORM;


interface CrudDao extends ORMDao
{
    public function getList($amount = 20, $page = 1);

    public function exists($key);

    public function get($key);

    public function save(Entity $entity);

    public function delete($key);
}
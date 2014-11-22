<?php

namespace Panda\ORM;


interface Entity extends \ArrayAccess, \Iterator
{
    public function hydrate(array $args);
}
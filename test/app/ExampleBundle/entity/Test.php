<?php

namespace PandaTest\ExampleBundle\entity;

use Doctrine\ORM\Mapping as ORM;
use Panda\ORM\AbstractEntity;

/**
* @ORM\Entity
* @ORM\Table(name="tests")
*/
class Test extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue
     */
    protected $id = null;

    /**
     * @ORM\Column(type="string", name="label", length=100, unique=false, nullable=false)
     */
    protected $label = null;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }


}

<?php

namespace PandaTest\ExampleBundle\dao;

use Doctrine\ORM\Tools\SchemaTool;
use Panda\ORM\Doctrine\AbstractDoctrineBaseDao;

class TestDoctrineORMDao extends AbstractDoctrineBaseDao
{
    public function createTestDb()
    {
        $em = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $classes = array(
            $em->getClassMetadata('PandaTest\ExampleBundle\entity\Test')
        );

        $tool->dropDatabase();
        $tool->createSchema($classes);

        $testEntity = $this->newEntity('Test', array('id' => 1, 'label' => 'test'));
        $em->persist($testEntity);
        $em->flush();
    }

    public function selectTestResults()
    {
        return $this->getRepository('Test')->findAll();
    }
}
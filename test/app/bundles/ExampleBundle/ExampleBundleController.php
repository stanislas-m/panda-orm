<?php

namespace PandaTest\ExampleBundle;

use Panda\Core\Component\Bundle\AbstractController;

class ExampleBundleController extends AbstractController
{
    public function testHomeAction($name = 'panda')
    {
        $this->getDao('TestDoctrineORM')->createTestDb();
        $this->view->setVar('name', htmlspecialchars($name));
        $this->view->setVar('queryResults', $this->getDao('TestDoctrineORM')->selectTestResults());
        return "home.php";
    }
} 
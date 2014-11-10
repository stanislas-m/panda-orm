<?php

namespace PandaTest\ExampleBundle;

use Panda\Core\Component\Bundle\AbstractController;

class ExampleBundleController extends AbstractController
{
    public function testHomeAction($name = 'panda')
    {
        $this->view->setVar('name', htmlspecialchars($name));
        return "home.php";
    }
} 
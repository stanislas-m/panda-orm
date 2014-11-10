<?php

namespace Panda\ORM;

use Panda\Core\Component\Bundle\Dao\AbstractBasicDao;
use Panda\Core\Component\Config\ConfigManager;
use Spot\Config;
use Spot\Locator;

class AbstractSpotBaseDao extends AbstractBasicDao implements ORMDao
{
    protected static $config;
    protected static $locator;

    public function __construct()
    {
        if (!class_exists('Spot\Config') || !class_exists('Spot\Locator')) {
            throw new \RuntimeException('Unable to use Spot-based DAO: missing "vlucas/spot2" dependency.');
        }
        if (self::$config === null) {
            self::$config = new Config();
        }
    }

    public function getEntityManager($entityManagerName)
    {
        if ($entityManagerName === null) {
            $entityManagerName = ConfigManager::get('database.default');
        }

        if (!array_key_exists(self::$connections, $entityManagerName)) {

            $connectionParams = ConfigManager::get('database.list.' . $entityManagerName);

            self::$config->addConnection($entityManagerName, $connectionParams);

            if (self::$locator === null) {
                self::$locator = new Locator(self::$config);
            } else {
                self::$locator->config(self::$config);
            }
        }

        return self::$locator;
    }
}
<?php

namespace Panda\ORM\Spot;

use Panda\Core\Component\Bundle\Dao\AbstractBasicDao;
use Panda\Core\Component\Config\ConfigManager;
use Panda\ORM\Entity;
use Panda\ORM\ORMDao;
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

    public function getEntityManager($datasourceName = null)
    {
        if ($datasourceName === null) {
            $datasourceName = ConfigManager::get('database.default');
        }

        try {
            if (self::$locator === null || self::$locator->config()->connection() === false) {

                $connectionParams = ConfigManager::get('database.list.' . $datasourceName);

                self::$config->addConnection($datasourceName, $connectionParams);

                if (self::$locator === null) {
                    self::$locator = new Locator(self::$config);
                } else {
                    self::$locator->config(self::$config);
                }
            }
        } catch (Exception $e) {
            throw new \RuntimeException('Unknown "'.$datasourceName.'" entity manager.');
        }

        return self::$locator;
    }

    /**
     * @param $entityName
     * @param $args
     * @return mixed
     */
    public function newEntity($entityName, array $args = array())
    {
        $entity = new $entityName();

        if (!empty($args)) {
            $entity->hydrate($args);
        }

        return $entity;
    }

    /**
     * @param Entity $entity
     * @param $args
     * @return Entity
     */
    public function editEntity(Entity $entity, array $args = array())
    {
        $entity->hydrate($args);
        return $entity;
    }
}
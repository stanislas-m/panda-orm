<?php

namespace Panda\ORM\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Gedmo\DoctrineExtensions;
use Panda\Core\Component\Bundle\Dao\AbstractBasicDao;
use Panda\Core\Component\Config\ConfigManager;
use Panda\ORM\Entity;
use Panda\ORM\ORMDao;
use ReflectionClass;

class AbstractDoctrineBaseDao extends AbstractBasicDao implements ORMDao
{
    protected static $entityManagers = array();
    protected $eventManager;

    /**
     * @param $datasourceName
     * @return mixed
     */
    public function getEntityManager($datasourceName = null)
    {
        if ($datasourceName === null) {
            $datasourceName = ConfigManager::get('datasources.default');
        } else {
            if (!is_string($datasourceName) || empty($datasourceName)) {
                throw new \InvalidArgumentException('Invalid datasource name "'.((string) $datasourceName).'"');
            }
        }

        if (!array_key_exists($datasourceName, self::$connections)) {

            $isDebug = ConfigManager::exists('datasources.debug') ? ConfigManager::get('datasources.debug') : false;

            $cacheName = ConfigManager::get('datasources.list.' . $datasourceName . '.cache');
            $cacheClass = '\Doctrine\Common\Cache\\' . (!empty($cacheName) ? $cacheName : 'ArrayCache');

            $entityManagerConfig = Setup::createConfiguration($isDebug);

            //Load annotations stuff
            AnnotationRegistry::registerFile(VENDORS_DIR .
                'doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
            $cache = new $cacheClass();
            $annotationReader = new AnnotationReader();
            $cachedAnnotationReader = new CachedReader(
                $annotationReader,
                $cache,
                $isDebug
            );

            //Load event manager
            $this->eventManager = new EventManager();
            $listenersList = ConfigManager::get('datasources.listeners');
            if (!empty($listenersList)) {
                foreach ($listenersList as $listener) {
                    $l = new $listener();
                    $this->eventManager->addEventSubscriber($l);
                }
            }

            //Tables prefix
            if (ConfigManager::exists('datasources.list.' . $datasourceName . '.prefix')) {
                $tablePrefix = new DoctrineTablePrefixListener(ConfigManager::get('datasources.list.' . $datasourceName
                    . '.prefix'));
                $this->eventManager->addEventListener(Events::loadClassMetadata, $tablePrefix);
            }

            //Load doctrine extensions listeners if any
            if (!empty($listenersList) && class_exists('\Gedmo\DoctrineExtensions')) {
                $driverChain = new MappingDriverChain();
                DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
                    $driverChain,
                    $cachedAnnotationReader
                );
            }

            $driver = new AnnotationDriver(
                $cachedAnnotationReader,
                glob(
                    APP_DIR  . '*/entity'
                )
            );

            $entityManagerConfig->setMetadataDriverImpl($driver);

            self::$entityManagers[$datasourceName] = EntityManager::create(
                ConfigManager::get('datasources.list.' . $datasourceName),
                $entityManagerConfig,
                $this->eventManager
            );
        }
        return self::$entityManagers[$datasourceName];
    }

    public function getRepository($entityName, $datasourceName = null)
    {
        $reflClass = new ReflectionClass($this);
        $namespaceRaw = substr($reflClass->getNamespaceName(), 0, -4);
        $bundleName = substr($namespaceRaw, strpos($namespaceRaw, '\\') + 1);
        $namespace = substr($namespaceRaw, 0, -strlen($bundleName) - 1);
        $em = $this->getEntityManager($datasourceName);

        if (!class_exists($entityName)) {
            if (class_exists($namespace. '\\' . $entityName)) {
                $entityName = $namespace. '\\' . $entityName;
            } else if (class_exists($namespace . '\\' . $bundleName . '\\entity\\' . $entityName)) {
                $entityName = $namespace . '\\' . $bundleName . '\\entity\\' . $entityName;
            }
        }

        return $em->getRepository($entityName);
    }

    /**
     * @param $entityName
     * @param $args
     * @return mixed
     */
    public function newEntity($entityName, array $args = array())
    {
        $reflClass = new ReflectionClass($this);
        $namespaceRaw = substr($reflClass->getNamespaceName(), 0, -4);
        $bundleName = substr($namespaceRaw, strpos($namespaceRaw, '\\') + 1);
        $namespace = substr($namespaceRaw, 0, -strlen($bundleName) - 1);

        if (class_exists($entityName)) {
            $entity = new $entityName();
        } else if (class_exists($namespace . '\\' . $entityName)) {
            $className = $namespace . '\\' . $entityName;
            $entity = new $className();
        } else if (class_exists($namespace . '\\' . $bundleName . '\\entity\\' . $entityName)) {
            $className = $namespace . '\\' . $bundleName . '\\entity\\' . $entityName;
            $entity = new $className();
        }

        if (!isset($entity) || !is_subclass_of($entity, 'Panda\ORM\AbstractEntity')) {
            throw new InvalidArgumentException('The class "' . $entityName . '" must extends from "Panda\ORM\AbstractEntity"');
        }

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
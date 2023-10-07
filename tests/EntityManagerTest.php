<?php
namespace Tests;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use PHPUnit\Framework\TestCase;

class EntityManagerTest extends TestCase
{

    public static EntityManager $entityManager;

    public static function setUpBeforeClass(): void
    {
        DotEnvLoader::loadEnvironment();
    }

    /**
     * @throws MissingMappingDriverImplementation
     * @throws Exception
     */
    public function testGetEntityManagerWithDefaultParameters()
    {

        self::$entityManager = EntityManagerFactory::getEntityManager($_ENV["DSN"]);
        $this->assertInstanceOf(EntityManager::class, self::$entityManager);
    }


    /**
     * @throws MissingMappingDriverImplementation
     * @throws Exception
     */
    public function testGetEntityManagerWithInvalidDsn()
    {
        $dsn = 'invalid-dsn';
        $this->expectException(Exception::class);
        EntityManagerFactory::getEntityManager($dsn);
    }
}
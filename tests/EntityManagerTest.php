<?php

namespace Tests;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

class EntityManagerTest extends TestCase
{

    public static EntityManager $entityManager;
    private static SchemaTool $schemaTool;

    /**
     * @throws \Doctrine\ORM\Exception\MissingMappingDriverImplementation
     * @throws \Doctrine\ORM\Tools\ToolsException
     * @throws \Doctrine\DBAL\Exception
     */
    public static function setUpBeforeClass(): void
    {

        // Initialize the entity manager and schema tool here
        $dbParams = ["driver" => $_ENV["DB_DRIVER"], "database_name" => $_ENV["DB_NAME"],];
        $entitiesPath = [__DIR__ . '/entities'];
        self::$entityManager = EntityManagerFactory::getEntityManager($dbParams, $entitiesPath);
        self::$schemaTool = new SchemaTool(self::$entityManager);
        $metadata = self::$entityManager->getMetadataFactory()->getAllMetadata();
        self::$schemaTool->createSchema($metadata);
    }

    /**
     */
    public function testGetEntityManagerWithDefaultParameters()
    {
        $this->assertInstanceOf(EntityManager::class, self::$entityManager);
    }


    /**
     * @throws MissingMappingDriverImplementation
     * @throws Exception
     */
    public function testGetEntityManagerWithInvalidDatabaseParameters()
    {
        $dbParams = ["driver" => 'unsupported_driver', "database_name" => $_ENV["DB_NAME"],];
        $entitiesPath = [__DIR__ . '/entities'];
        $this->expectException(Exception::class);
        EntityManagerFactory::getEntityManager($dbParams, $entitiesPath);
    }

    public static function tearDownAfterClass(): void
    {
        // Drop the schema after all tests have run
        self::$schemaTool->dropDatabase();
    }
}
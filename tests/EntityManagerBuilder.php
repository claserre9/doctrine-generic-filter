<?php
namespace Tests;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\ORMSetup;

class EntityManagerBuilder
{
    /**
     * Retrieves the entity manager.
     *
     * @param string $dsn The DSN for the database.
     *      Defaults to the SQLite database in the "../data/db.sqlite" file.
     * @param array $entitiesPath An array of paths to the entities.
     *      Defaults to the "../src" directory.
     * @return EntityManager|null The entity manager instance.
     * @throws Exception | MissingMappingDriverImplementation
     */
    public static function getEntityManager(
        string $dsn = 'mysqli://root:@localhost/test',
        array $entitiesPath = array(__DIR__ . '/../tests')): ?EntityManager
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(paths: $entitiesPath, isDevMode: true,);
        $dsnParser = new DsnParser();
        $connectionParams = $dsnParser->parse($dsn);
        $connection = DriverManager::getConnection($connectionParams);
        return new EntityManager($connection, $config);
    }
}
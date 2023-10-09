<?php

namespace Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMSetup;

class EntityManagerFactory
{
    /**
     * Retrieves the entity manager.
     *
     * @param array $dbParams Database connection parameters.
     *   - 'driver' (string): The database driver (e.g., mysql, sqlite).
     *   - 'host' (string): The database host.
     *   - 'username' (string): The database username.
     *   - 'password' (string): The database password.
     *   - 'database_name' (string): The database name.
     * @param array $entitiesPath An array of paths to the entities.
     * @return EntityManager|null The entity manager instance.
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\MissingMappingDriverImplementation
     */
    public static function getEntityManager(array $dbParams, array $entitiesPath = [__DIR__ . '/../tests/']): ?EntityManager
    {
        if ($dbParams['driver'] === 'sqlite') {
            // For SQLite, adjust the connection parameters and path
            $dbParams = [
                'url' => 'sqlite:///' . $dbParams['database_name'],
            ];
        }

        $config = self::createConfiguration($entitiesPath);
        $connection = self::createConnection($dbParams);
        return new EntityManager($connection, $config);
    }


    /**
     * Create a Doctrine DBAL connection.
     *
     * @param array $dbParams Database connection parameters.
     * @return Connection The DBAL connection.
     * @throws \Doctrine\DBAL\Exception
     */
    private static function createConnection(array $dbParams): Connection
    {
        return DriverManager::getConnection($dbParams);
    }


    /**
     * Create a Doctrine ORM Configuration based on the mapping type.
     *
     * @param array $entitiesPath An array of paths to the entities.
     * @return Configuration The ORM configuration.
     */
    private static function createConfiguration(array $entitiesPath): Configuration
    {

        return ORMSetup::createAttributeMetadataConfiguration(
            paths:$entitiesPath,
            isDevMode: true,
        );
    }
}

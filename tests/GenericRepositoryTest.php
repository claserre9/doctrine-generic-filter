<?php

namespace Tests;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use PHPUnit\Framework\TestCase;


class GenericRepositoryTest extends TestCase
{
    public static ?EntityManager $entityManager;

    /**
     * @throws MissingMappingDriverImplementation
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {

        self::$entityManager = EntityManagerBuilder::getEntityManager();
        $connection = self::$entityManager->getConnection();
        $sqlFile = __DIR__.'/user.sql';
        $sqlQueries = file_get_contents($sqlFile);
        $queries = explode(';', $sqlQueries);
        foreach ($queries as $query) {
            // Trim the query to remove leading/trailing whitespace and empty lines
            $query = trim($query);

            // Check if the query is not empty
            if (!empty($query)) {
                // Execute the SQL query
                $connection->executeStatement($query);
            }
        }


    }

    /**
     */
    public function testMyTest(){



        $this->assertTrue(true);
    }

}

<?php

namespace Tests;
use App\GenericRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use PHPUnit\Framework\TestCase;
use Tests\entities\User;

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
        // Truncate table
        $connection->executeStatement("truncate table user");
        // Load fixtures
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
     * @throws \Exception
     */
    public function testGetPaginatedResults(){

        $queryParams = [
            'gender' => ["eq"=>'Male'],
        ];
        $results = (new GenericRepository(self::$entityManager))
            ->getPaginatedResults(User::class, 1, 10, $queryParams);

        $this->assertIsArray($results);
        $this->assertArrayHasKey('total', $results);
        $this->assertArrayHasKey('currentPage', $results);
        $this->assertArrayHasKey('totalPages', $results);
        $this->assertArrayHasKey('results', $results);
        $this->assertTrue(true);

        $this->assertEquals(497, $results['total']);
        $this->assertEquals(1, $results['currentPage']);
        $this->assertEquals(50, $results['totalPages']);
        $this->assertCount(10, $results['results']);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public static function tearDownAfterClass(): void
    {
        self::$entityManager->getConnection()->executeStatement("truncate table user");
    }

}

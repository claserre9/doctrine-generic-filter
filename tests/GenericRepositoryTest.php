<?php

namespace Tests;

use App\GenericRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Error;
use PHPUnit\Framework\TestCase;
use Tests\entities\User;

class GenericRepositoryTest extends TestCase
{
    public static ?EntityManager $entityManager;
    public static GenericRepository $genericRepository;

    /**
     * @throws MissingMappingDriverImplementation
     * @throws DBALException
     */
    public static function setUpBeforeClass(): void
    {

        DotEnvLoader::loadEnvironment();
        self::$entityManager = EntityManagerFactory::getEntityManager($_ENV["DSN"]);
        $connection = self::$entityManager->getConnection();
        // Truncate table
        $connection->executeStatement("truncate table user");
        // Load fixtures
        $sqlFile = __DIR__ . '/user.sql';
        $sqlQueries = file_get_contents($sqlFile);
        $queries = explode(';', $sqlQueries);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $connection->executeStatement($query);
            }
        }
        self::$genericRepository = new GenericRepository(self::$entityManager);


    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForGenderWithEqualExpression()
    {

        $queryParams = ['gender' => ["eq" => 'Male'],];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 497, 1, 50, 10);
    }


    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForGenderWithNotEqualExpression()
    {
        $queryParams = ['gender' => ["neq" => 'Male'],];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 503, 1, 51, 10);

    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithLessThanOrEqual(){
        $queryParams = ['age' => ["lte" => 20]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 90, 1, 9, 10);
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithGreaterThanOrEqual(){
        $queryParams = ['age' => ["gte" => 20]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 928, 1, 93, 10);
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithGreaterThanOrEqualAndLessThan(){
        $queryParams = ['age' => ["gte" => 20, "lt" => 30]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 177, 1, 18, 10);
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithBetween(){
        $queryParams = ['age' => ["between" => ["start" => 20, "end" => 30]]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 198, 1, 20, 10);
    }


    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithInExpressionWithArray(){
        $queryParams = [
            'country' => ["in" => ["China", "Indonesia"]]
        ];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 301, 1, 31, 10);
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithInExpressionWithString(){
        $queryParams = [
            'country' => ["in" => "China, Indonesia"]
        ];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 301, 1, 31, 10);
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsWithInvalidExpression(){
        $queryParams = [
            'country' => ["invalidExpression" => ["China", "Indonesia"]]
        ];
        $this->expectException(Error::class);
        self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
    }



    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public static function tearDownAfterClass(): void
    {
        self::$entityManager->getConnection()->executeStatement("truncate table user");
    }

    /**
     * @param array $results
     * @param $total
     * @param $currentPage
     * @param $totalPages
     * @param $totalResults
     * @return void
     */
    private function assertResult(array $results, $total, $currentPage, $totalPages, $totalResults): void
    {
        $this->assertIsArray($results);
        $this->assertArrayHasKey('total', $results);
        $this->assertArrayHasKey('currentPage', $results);
        $this->assertArrayHasKey('totalPages', $results);
        $this->assertArrayHasKey('results', $results);
        $this->assertEquals($total, $results['total']);
        $this->assertEquals($currentPage, $results['currentPage']);
        $this->assertEquals($totalPages, $results['totalPages']);
        $this->assertCount($totalResults, $results['results']);
    }

}

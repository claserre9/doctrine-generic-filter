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

//        DotEnvLoader::loadEnvironment();
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

    public function testGetPaginatedResultsHaveSpecifiedKeysInResult()
    {
        $results = self::$genericRepository->getPaginatedResults(User::class);
        $this->assertResultKeys($results);
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForGenderWithEqualExpression()
    {

        $queryParams = ['gender' => ["eq" => 'Male'],];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 497, 50);
    }


    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForGenderWithNotEqualExpression()
    {
        $queryParams = ['gender' => ["neq" => 'Male'],];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 503, 51);

    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithLessThanOrEqual()
    {
        $queryParams = ['age' => ["lte" => 20]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 90, 9 );
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithGreaterThanOrEqual()
    {
        $queryParams = ['age' => ["gte" => 20]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 928, 93 );
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithGreaterThanOrEqualAndLessThan()
    {
        $queryParams = ['age' => ["gte" => 20, "lt" => 30]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 177, 18);
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithBetween()
    {
        $queryParams = ['age' => ["between" => ["start" => 20, "end" => 30]]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 198, 20);
    }


    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithInExpressionWithArray()
    {
        $queryParams = ['country' => ["in" => ["China", "Indonesia"]]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 301, 31);
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsForAgeWithInExpressionWithString()
    {
        $queryParams = ['country' => ["in" => "China, Indonesia"]];
        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
        $this->assertResult($results, 301, 31);
    }

    /**
     * @throws \Exception
     */
    public function testGetPaginatedResultsWithInvalidExpression()
    {
        $queryParams = ['country' => ["invalidExpression" => ["China", "Indonesia"]]];
        $this->expectException(Error::class);
        self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams);
    }

    public function testGetPaginatedResultsForSortingByAge(){
        $orderBy = [
            'age' => 'DESC',
            'id' => 'DESC'
        ];

        $queryParams = [
            'country' => ['eq' => 'China'],
            'gender' => ['eq' => 'Male'],
        ];

        $results = self::$genericRepository->getPaginatedResults(User::class, 1, 10, $queryParams, $orderBy);
        /** @var User $firstUser */
        $firstUser = $results['data'][0];
        $this->assertEquals(594, $firstUser->getId());
        $this->assertEquals(75, $firstUser->getAge());
        $this->assertEquals('China', $firstUser->getCountry());
        $this->assertEquals('Male', $firstUser->getGender());
        $this->assertEquals('Teador Toffetto', $firstUser->getName());
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
     * @param $totalItems
     * @param $totalPages
     * @param int $currentPage
     * @param int $itemsPerPage
     * @return void
     */
    private function assertResult(array $results, $totalItems, $totalPages, $currentPage = 1, $itemsPerPage = 10,): void
    {
        $this->assertEquals($totalItems, $results['meta']['total']);
        $this->assertEquals($totalPages, $results['meta']['lastPage']);
        $this->assertEquals($currentPage, $results['meta']['currentPage']);
        $this->assertEquals($itemsPerPage, $results['meta']['perPage']);

    }

    private function assertResultKeys($results): void
    {
        $this->assertIsArray($results);
        $this->assertArrayHasKey('data', $results);
        $this->assertArrayHasKey('meta', $results);
        $this->assertArrayHasKey('total', $results['meta']);
        $this->assertArrayHasKey('perPage', $results['meta']);
        $this->assertArrayHasKey('currentPage', $results['meta']);
        $this->assertArrayHasKey('lastPage', $results['meta']);
        $this->assertArrayHasKey('from', $results['meta']);
        $this->assertArrayHasKey('to', $results['meta']);


    }

}

<?php
namespace Tests;

use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Tools\SchemaTool;
use Tests\entities\User;
use PHPUnit\Framework\TestCase;


class UserRepositoryTest extends TestCase
{

    public static EntityManager $entityManager;
    private static SchemaTool $schemaTool;


    /**
     * @throws MissingMappingDriverImplementation
     * @throws Exception|\Doctrine\ORM\Tools\ToolsException
     */
    public static function setUpBeforeClass(): void
    {
        $dbParams = ["driver" => $_ENV["DB_DRIVER"], "database_name" => $_ENV["DB_NAME"],];
        $entitiesPath = [__DIR__ . '/entities'];
        self::$entityManager = EntityManagerFactory::getEntityManager($dbParams, $entitiesPath);
        self::$schemaTool = new SchemaTool(self::$entityManager);
        $metadata = self::$entityManager->getMetadataFactory()->getAllMetadata();
        self::$schemaTool->createSchema($metadata);

    }

    /**
     * @throws OptimisticLockException
     * @throws NotSupported
     * @throws ORMException
     */
    public function testCreateEntity()
    {
        $entity = new User();
        $entity
            ->setName('Test Entity')
            ->setAge(100)
            ->setEmail('admin@localhost')
            ->setGender('Male')
            ->setCountry("Test Country")
            ->setPhone("1234567890")
            ->setBirthday(new DateTime())
        ;


        self::$entityManager->persist($entity);
        self::$entityManager->flush();

        $repository = self::$entityManager->getRepository(User::class);
        $persistedEntity = $repository->findOneBy(['name' => 'Test Entity']);

        $this->assertInstanceOf(User::class, $persistedEntity);
        $this->assertEquals('Test Entity', $persistedEntity->getName());
    }

    /**
     * @throws NotSupported
     */
    public function testFindById()
    {
        $entity = self::$entityManager->getRepository(User::class)->findOneBy(['id' => 1]);
        $this->assertInstanceOf(User::class, $entity);
    }
    /**
     */
    public static function tearDownAfterClass(): void
    {
        // Drop the schema after all tests have run
        self::$schemaTool->dropDatabase();
    }

}
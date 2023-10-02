<?php
namespace Tests;

use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Tests\entities\User;
use PHPUnit\Framework\TestCase;


class UserRepositoryTest extends TestCase
{

    public static ?EntityManager $entityManager;


    /**
     * @throws MissingMappingDriverImplementation
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$entityManager = EntityManagerBuilder::getEntityManager();

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
            ->setPassword("12345678")
            ->setAddress("Test Address")
            ->setCity("Test City")
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
     * @throws Exception
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::$entityManager->getConnection()->executeQuery("TRUNCATE TABLE user");
    }

}
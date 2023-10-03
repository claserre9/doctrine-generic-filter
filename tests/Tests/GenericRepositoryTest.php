<?php

namespace Tests;
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
        $dsn = $_ENV['DSN'];
        self::$entityManager = EntityManagerBuilder::getEntityManager();

    }

}

<?php

namespace Tests;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use PHPUnit\Framework\TestCase;

class GenericRepositoryTest extends TestCase
{
    public static ?EntityManager $entityManager;

    /**
     * @throws MissingMappingDriverImplementation
     * @throws \Doctrine\DBAL\Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$entityManager = EntityManagerBuilder::getEntityManager();

    }

}

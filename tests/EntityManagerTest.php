<?php
namespace Tests;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use PHPUnit\Framework\TestCase;

class EntityManagerTest extends TestCase
{
    /**
     * @throws MissingMappingDriverImplementation
     * @throws Exception
     */
    public function testGetEntityManagerWithDefaultParameters()
    {;
        $entityManager = EntityManagerBuilder::getEntityManager();
        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }


    /**
     * @throws MissingMappingDriverImplementation
     * @throws Exception
     */
    public function testGetEntityManagerWithInvalidDsn()
    {
        $dsn = 'invalid-dsn';
        $this->expectException(Exception::class);
        EntityManagerBuilder::getEntityManager($dsn);
    }
}
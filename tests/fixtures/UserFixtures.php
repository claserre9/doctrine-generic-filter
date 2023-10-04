<?php

namespace Tests\fixtures;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Tests\entities\User;

class UserFixtures implements FixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        // Create and persist entities with test data
        $faker = Factory::create();
        for ($i = 0; $i < 200; $i++) {
            $user = new User();
            $user->setName($faker->name);
            $user->setAge($faker->numberBetween(1, 90));
            $user->setEmail($faker->email);
            $user->setBirthday(new \DateTime($faker->date));
            $user->setPassword($faker->password);
            $user->setAddress($faker->address);
            $user->setCity($faker->city);
            $user->setCountry($faker->country);
            $user->setPhone($faker->phoneNumber);
            $manager->persist($user);
            $manager->flush();
        }

    }
}
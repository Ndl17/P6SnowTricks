<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UsersFixtures extends Fixture implements OrderedFixtureInterface
{

    public const USER_COUNT = 5;

    public function getOrder()
    {
        return 1;
    }
    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= self::USER_COUNT; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email);
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword(password_hash($faker->password(15), PASSWORD_BCRYPT));
            $user->setPseudo($faker->unique()->firstName(20));
            $user->setIsVerified(true);
            $user->setResetToken('');
            $manager->persist($user);
            $this->setReference('user_' . $i, $user);
        }

        // Création compte test
        $testUserVerified = new User();
        $testUserVerified->setEmail('user@snowtricks.com');
        $testUserVerified->setRoles(['ROLE_USER']);
        $testUserVerified->setPassword(password_hash('123456', PASSWORD_BCRYPT));
        $testUserVerified->setPseudo('admin');
        $testUserVerified->setIsVerified(true);
        $testUserVerified->setResetToken('');
        $manager->persist($testUserVerified);
        $this->setReference('admin_', $testUserVerified);

        $testUserUnverified = new User();
        $testUserUnverified->setEmail('user2@snowtricks.com');
        $testUserUnverified->setRoles(['ROLE_USER']);
        $testUserUnverified->setPassword(password_hash('123456', PASSWORD_BCRYPT));
        $testUserUnverified->setPseudo('user');
        $testUserUnverified->setIsVerified(false);
        $testUserUnverified->setResetToken('');
        $manager->persist($testUserUnverified);
        $this->setReference('userUnver_', $testUserUnverified);

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;
class UsersFixtures extends Fixture
{


  public function load(ObjectManager $manager): void
  {
    // use the factory to create a Faker\Generator instance
    $faker = Faker\Factory::create('fr_FR');

    for($i = 1; $i <= 10; $i++){
      $user = new User();
      $user->setEmail($faker->unique()->email);
      $user->setRoles(["user"]);
      $user->setPassword(password_hash($faker->password(15), PASSWORD_BCRYPT));
      $user->setPseudo($faker->unique()->firstName(20));
      $manager->persist($user);
        $this->setReference('user_' . $i, $user);
    }


    $manager->flush();
  }
}

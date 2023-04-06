<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Figure;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;


class CommentFixtures extends Fixture implements DependentFixtureInterface
{


  public function load(ObjectManager $manager): void
  {
    // use the factory to create a Faker\Generator instance
    $faker = Faker\Factory::create('fr_FR');
    
    for($i = 1; $i <= 10; $i++){
      $user = $this->getReference('user_'. $i);
      $figure = $this->getReference('figure_'. $i);
      $comment = new Comment();
      $comment->setContent($faker->text(255));
      $comment->setCreatedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $faker->date('Y-m-d H:i:s')));
      $comment->setIdpseudo($user);
      $comment->setIdfigure($figure);
      $manager->persist($comment);
    }


    $manager->flush();
  }

  public function getDependencies()
  {
    return [
      UsersFixtures::class,
      FigureFixtures::class,
    ];
  }


}

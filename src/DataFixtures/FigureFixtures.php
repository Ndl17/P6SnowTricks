<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;
use DateTimeImmutable;
class FigureFixtures extends Fixture
{
  public function __construct(private SluggerInterface $slugger){}
    /*  public function load(ObjectManager $manager): void
    {

    $this->createFigure('Test2', 'testestetetete', 'test2', 'img2', 'vid2', $manager);
    $this->createFigure('Test3', 'testestetetete', 'test3', 'img3', 'vid3', $manager);

    $manager->flush();
  }

  public function createFigure(string $name, string $description, string $type, string $imgName, string $vidName,ObjectManager $manager)
  {
  $figure = new Figure();
  $figure->setName($name);
  $figure->setDescription($description);
  $figure->setType($type);
  $figure->setImgName($imgName);
  $figure->setVidName($vidName);
  $figure->setSlug($this->slugger->slug($figure->getName())->lower());
  $manager->persist($figure);

  return $figure;
}
}*/


public function load(ObjectManager $manager): void
{
  // use the factory to create a Faker\Generator instance
  $faker = Faker\Factory::create('fr_FR');
  for($i = 1; $i <= 10; $i++){
    $user = $this->getReference('user_'. $i);
    $figure = new Figure();
    $figure->setName($faker->text(15));
    $figure->setDescription($faker->text(1000));
    $figure->setType($faker->text(15));
    $figure->setImgName($faker->text(15));
    $figure->setVidName($faker->text(15));
    $figure->setSlug($this->slugger->slug($figure->getName())->lower());
    $figure->setCreatedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $faker->date('Y-m-d H:i:s')));
    $figure->setModifiedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $faker->date('Y-m-d H:i:s')));
    $figure->setUserId($user);
    $manager->persist($figure);

    $this->setReference('figure_' . $i, $figure);

  }
  $manager->flush();

}


}

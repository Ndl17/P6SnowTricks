<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureFixtures extends Fixture
{
  public function __construct(private SluggerInterface $slugger){}
    public function load(ObjectManager $manager): void
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
  }

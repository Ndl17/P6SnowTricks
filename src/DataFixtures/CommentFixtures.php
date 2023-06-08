<?php

namespace App\DataFixtures;

use App\DataFixtures\FigureFixtures;
use App\Entity\Comment;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * controller gérant les fixtures des commentaires de l'application
 */

class CommentFixtures extends Fixture implements OrderedFixtureInterface
{

    public const USER_COUNT = UsersFixtures::USER_COUNT;
    public const FIGURE_COUNT = FigureFixtures::FIGURE_COUNT;

    public function getOrder()
    {
        return 4;
    }

    /**
     * génère des commentaires fictifs
     * @param \Doctrine\Persistence\ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 165; $i++) {
            $comment = new Comment();
            for ($j = 1; $j <= self::USER_COUNT; $j++) {
                $userRand = mt_rand(1, $j);
                $user = $this->getReference('user_' . $userRand);
                $comment->setUser($user);
            }

            for ($k = 1; $k <= self::FIGURE_COUNT; $k++) {
                $figureRand = mt_rand(1, $k);
                $figure = $this->getReference('figure_' . $figureRand);
                $comment->setFigure($figure);
            }

            $comment->setContent($faker->text(255));
            $comment->setCreatedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $faker->date('Y-m-d H:i:s')));

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies():array
    {
        return [
            UsersFixtures::class,
            FigureFixtures::class,
        ];
    }

}

<?php

namespace App\DataFixtures;

use App\Entity\Groupe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GroupeFixtures extends Fixture implements OrderedFixtureInterface
{

    public const GROUP_COUNT = 4;

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager): void
    {

        $data = [
            ['name' => 'Groupe 1'],
            ['name' => 'Groupe 2'],
            ['name' => 'Groupe 3'],
            ['name' => 'Groupe 4'],
            ['name' => 'Groupe 5'],
        ];

        foreach ($data as $i => $item) {
            $groupe = new Groupe();
            $groupe->setName($item['name']);
            $manager->persist($groupe);
            $this->addReference('groupe_' . $i, $groupe);
        }
        $manager->flush();
    }

}

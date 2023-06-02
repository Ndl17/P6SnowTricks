<?php

namespace App\DataFixtures;

use App\DataFixtures\GroupeFixtures;
use App\DataFixtures\UsersFixtures;
use App\Entity\Figure;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureFixtures extends Fixture implements OrderedFixtureInterface
{
    private $slugger;
    public const USER_COUNT = UsersFixtures::USER_COUNT;
    public const GROUP_COUNT = GroupeFixtures::GROUP_COUNT;
    public const FIGURE_COUNT = 15;

    public function getOrder()
    {
        return 3;
    }

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {

        $data = [
            ['title' => 'Mute', 'content' => 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant '],
            ['title' => 'Style Week', 'content' => 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant'],
            ['title' => 'Indy', 'content' => 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière'],
            ['title' => 'Stalefish ', 'content' => 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière'],
            ['title' => 'Tail grab', 'content' => 'Saisie de la partie arrière de la planche, avec la main arrière'],
            ['title' => 'Nose grab', 'content' => 'Saisie de la partie avant de la planche, avec la main avant'],
            ['title' => 'Japan air', 'content' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside'],
            ['title' => 'Seat belt', 'content' => 'Saisie du carre frontside à l\'arrière avec la main avant'],
            ['title' => 'Truck driver', 'content' => 'Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)'],
            ['title' => 'One foot tricks', 'content' => 'Figures réalisée avec un pied décroché de la fixation, afin de tendre la jambe correspondante pour mettre en évidence le fait que le pied n\'est pas fixé. Ce type de figure est extrêmement dangereuse pour les ligaments du genou en cas de mauvaise réception.'],
            ['title' => 'Tail Press', 'content' => 'Avant de faire une explication détaillée sur le Nose Press et le Tail Press, il faut d\'abord connaitre les termes anglais du Nose et du Tail, qui se traduisent par le nez et la queue en français. Sur un snowboard, on appelle le devant de la planche le Nose/nez et l\'arrière de la planche le Tail/queue. Que tu sois en Regular, en Goofy ou en Switch, le nez sera toujours pointé dans la direction de la piste..'],
            ['title' => 'Le Butter', 'content' => 'Le butter est la progression naturelle du Nose et du Tail Press, et il est à la base de nombreux tricks freestyle. Il est donc indispensable de le réussir.'],
            ['title' => 'Le Jib', 'content' => 'Le Jib est l\'une des figures de base à apprendre quand tu te lances dans le park, car il est utilisé sur la plupart des figures freestyle. Le jibbing consiste à chevaucher, sauter ou glisser sur tout ce qui n\'est pas une surface piquée, comme les rails, les bancs ou une bûche'],
            ['title' => 'Le Ollie/Nollie', 'content' => 'Pour faire un Ollie, déplace ton poids sur ta jambe arrière, comme pour les Tails Press, et dans un mouvement rapide, saute en levant ta jambe avant. Puis, avec un peu d\'effort, lève également ta jambe arrière, de sorte que tes pieds soient parallèles et que ta planche soit à l\'horizontale par rapport au sol. '],
            ['title' => 'Le Frontside', 'content' => 'Tu verras que faire des rotations sur la neige ou dans les airs avec ton snowboard est une chose amusante quelle que soit la situation. Toutes les rotations en snowboard sont exprimées en degrés.'],
            ['title' => 'Le Indy Grab', 'content' => 'Pour faire un Indy Grab, il faut décoller d\'un kicker et utiliser la technique du Ollie. Le Ollie te permettra de prendre plus de hauteur au départ du saut et de suffisamment plier tes jambes pour ensuite attraper ta planche en l\'air.'],
        ];

        // use the factory to create a Faker\Generator instance

        $faker = Factory::create('fr_FR');

        foreach ($data as $i => $item) {
            $figure = new Figure();
            for ($j = 1; $j <= self::USER_COUNT; $j++) {
                $user = $this->getReference('user_' . $j);
                $figure->setUser($user);
            }

            for ($k = 1; $k <= self::GROUP_COUNT; $k++) {
                $groupRand = mt_rand(1, $k);
                $group = $this->getReference('groupe_' . $groupRand);
                $figure->setGroupe($group);

            }

            $figure->setName($item['title']);
            $figure->setDescription($item['content']);
            $figure->setSlug($this->slugger->slug($figure->getName())->lower());
            $figure->setCreatedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $faker->date('Y-m-d H:i:s')));
            $figure->setModifiedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $faker->date('Y-m-d H:i:s')));

            $manager->persist($figure);
            $this->addReference('figure_' . $i, $figure);
        }
        $manager->flush();
    }

}

<?php
namespace App\DataFixtures;

use App\DataFixtures\FigureFixtures;
use App\Entity\Videos;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * controller gérant les fixtures des videos
 */

class VideosFixtures extends Fixture implements OrderedFixtureInterface
{
    private $slugger;
    public const FIGURE_COUNT = FigureFixtures::FIGURE_COUNT;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;

    }

    public function getOrder()
    {
        return 6;
    }

    /**
     * genère des videos fictives
     * @param \Doctrine\Persistence\ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {

        // Créez des images fictives
        $videosDatas = [
            [
                'url' => 'https://www.youtube.com/embed/V9xuy-rVj9w?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/8KotvBY28Mo?controls=0',
            ],
            //la
            [
                'url' => 'https://www.youtube.com/embed/SlhGVnFPTDE?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/mBB7CznvSPQ?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/EzGPmg4fFL8?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/SFYYzy0UF-8?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/t705_V-RDcQ?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/PxhfDec8Ays?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/0uGETVnkujA?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/_OMar04NRZw?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/SGs5hqP0B6o?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/2tFoHIYe6pE?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/ZFMC_l2VKfY?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/TiGkU_eXJa8?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/aINlzgrOovI?controls=0',
            ],
            [
                'url' => 'https://www.youtube.com/embed/p08ErTYMOh0?controls=0',
            ],

            // Ajoutez autant d'images que nécessaire
        ];

        foreach ($videosDatas as $i => $videosData) {
            $video = new Videos();

            // Générez un nom de fichier unique en utilisant le Slugger
            $originalFilename = $videosData['url'];

            // Définissez les propriétés de l'image
            $video->setUrl($originalFilename);

            $video->setFigure($this->getReference('figure_' . $i));

            // Persistez l'image
            $manager->persist($video);
        }

        $manager->flush();
    }
}

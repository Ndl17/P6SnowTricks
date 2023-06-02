<?php
namespace App\DataFixtures;

use App\DataFixtures\FigureFixtures;
use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageFixtures extends Fixture implements OrderedFixtureInterface
{
    private $slugger;
    public const FIGURE_COUNT = FigureFixtures::FIGURE_COUNT;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;

    }

    public function getOrder()
    {
        return 5;
    }
    public function load(ObjectManager $manager): void
    {

        // Créez des images fictives
        $imagesData = [
            [
                'filename' => 'fig1',
            ],
            [
                'filename' => 'fig2',
            ],
            [
                'filename' => 'fig3',
            ],
            [
                'filename' => 'fig4',
            ],
            [
                'filename' => 'fig5',
            ],
            [
                'filename' => 'fig6',
            ],
            [
                'filename' => 'fig7',
            ],
            [
                'filename' => 'fig8',
            ],
            [
                'filename' => 'fig9',
            ],
            [
                'filename' => 'fig10',
            ],
            [
                'filename' => 'fig11',
            ],
            [
                'filename' => 'fig12',
            ],
            [
                'filename' => 'fig13',
            ],
            [
                'filename' => 'fig14',
            ],
            [
                'filename' => 'fig15',
            ],
            [
                'filename' => 'fig16',
            ],

            // Ajoutez autant d'images que nécessaire
        ];

        foreach ($imagesData as $i => $imageData) {
            $image = new Images();

            // Générez un nom de fichier unique en utilisant le Slugger
            $originalFilename = $imageData['filename'];
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '.jpg';

            // Définissez les propriétés de l'image
            $image->setSlug($newFilename);
     
                $image->setFigure($this->getReference('figure_' . $i));

                // Persistez l'image
            $manager->persist($image);
        }

        $manager->flush();
    }
}

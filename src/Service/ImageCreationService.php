<?php

namespace App\Service;

use App\Entity\Figure;
use App\Entity\Images;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageCreationService
{

  private $params;

  public function __construct(ParameterBagInterface $params)
  {
    $this->params = $params;
  }

  public function addImage(array $images,Figure $figure,SluggerInterface $slugger) {
    foreach ($images as $image) {
      if ($image) {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        // on la slugifie
        $safeFilename = $slugger->slug($originalFilename);
        // on genere un nom unique
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

        // on deplace l'image dans le dossier images
    
          $image->move(
            $this->params->get('images_directory'),
            $newFilename
          );
     
        $img = new Images();
        $img->setSlug($newFilename);
        $figure->addImage($img);
      }
    }
  }


  //pas fini
}

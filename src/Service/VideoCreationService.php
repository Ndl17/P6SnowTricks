<?php

namespace App\Service;

use App\Entity\Figure;
use App\Entity\Videos;

class VideoCreationService
{
  public function addVideo(array $videos, Figure $figure) {
    foreach ($videos as $video) {
      if ($video) {
        $vid = new Videos();
        $vid->setUrl($video);
        $figure->addVideo($vid);
      }
    }
  }
}

<?php

namespace App\Controller;

use App\Entity\Videos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * controller gérant la suppression d'une video
 */
class VideosController extends AbstractController
{
    #[Route('/videos/delete/{id}', name:'videos_delete')]
    /**
     * gere la suppression d'une video
     * @param \App\Entity\Videos $videos
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteVideos(Videos $videos, EntityManagerInterface $entityManager): Response
    {
        // on verifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');

        // supprimer toutes la videos
        $entityManager->remove($videos);
        $entityManager->flush();
        $this->addFlash('success', 'La vidéo a bien été supprimée !');
        return $this->redirectToRoute('home_index');
    }
    
}

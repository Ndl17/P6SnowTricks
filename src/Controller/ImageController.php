<?php

namespace App\Controller;

use App\Entity\Images;
use App\Form\EditFigureImageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{

  
    
    #[Route('/image/delete/{id}', name:'image_delete')]
    public function deleteImage(Images $image, EntityManagerInterface $entityManager): Response
    {
        // on verifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        // remove the image file associated with the entity
        $imagePath = $this->getParameter('images_directory') . '/' . $image->getSlug();
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // remove the image entity from the database
        $entityManager->remove($image);
        $entityManager->flush();
        
        $this->addFlash('success', 'L\'image a bien été supprimée !');
        return $this->redirectToRoute('home_index');
    }

    
}

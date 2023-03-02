<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(FigureRepository $figureRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'figures' => $figureRepository->findBy([],['name' => 'asc']),
        ]);
    }
}

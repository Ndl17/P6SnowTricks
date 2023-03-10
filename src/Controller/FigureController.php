<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/home', name: 'home_')]
class FigureController extends AbstractController
{


  #[Route('/', name: 'index')]
  public function index(FigureRepository $figureRepository): Response
  {
    return $this->render('figure/index.html.twig', [
      'figures' => $figureRepository->findBy([],['name' => 'asc']),
    ]);
  }



  #[Route('/{slug}', name: 'details')]
  public function detail(Figure $figure): Response
  {
    return $this->render('figure/detail.html.twig', compact('figure'));
  }


}

<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Repository\FigureRepository;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

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
  public function detail(Figure $figure, CommentRepository $commentRepository, Request $request, EntityManagerInterface $entityManager): Response
  {
    $page = $request->query->getInt('page',1);
    $comments = $commentRepository->findCommentsByFigurePaginated($figure->getId(), $page);


    $comment = new Comment;
    // génération formulaire
    $commentForm = $this->createForm(CommentFormType::class, $comment);
    $commentForm->handleRequest($request);
    // traitement formulaire
    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
      $user = $this->getUser();
      if ($user == null ) {
        $this->addFlash('error', 'Vous n\'êtes pas connecté !');
        return $this->redirectToRoute('app_login');
      }else {

        $now = new DateTime();
        $createdAt = DateTimeImmutable::createFromMutable($now)->setTimezone(new DateTimeZone('UTC'));
        $comment->setCreatedAt($createdAt);
        $comment->setIdPseudo($user);
        $comment->setIdFigure($figure);
        $entityManager->persist($comment);
        $entityManager->flush();
        $this->addFlash('success', 'Votre commentaire a bien été envoyé !');
        return $this->redirectToRoute('home_details', ['slug' => $figure->getSlug()]);      }
      }



      return $this->render('figure/detail.html.twig', [
        'figure' => $figure,
        'comments' => $comments,
        'commentForm' => $commentForm->createView(),
      ]);
    }


  }

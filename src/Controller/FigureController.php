<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Repository\FigureRepository;
use App\Form\CommentFormType;
use App\Form\FigureFormType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
//use App\Security\Voter\FigureVoter;
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

  #[Route('/ajout', name: 'add')]
  public function addFig(Request $request, EntityManagerInterface $entityManager): Response
  {

    $this->denyAccessUnlessGranted('ROLE_USER');
    $figure = new Figure;
    $form = $this->createForm(FigureFormType::class, $figure);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $now = new DateTime();
      $createdAt = DateTimeImmutable::createFromMutable($now)->setTimezone(new DateTimeZone('UTC'));
      $figure->setCreatedAt($createdAt);
      $figure->setModifiedAt($createdAt);
      $slug = strtolower(str_replace(' ', '-', $figure->getName()));
      $figure->setSlug($slug);
      $figure->setUserId($this->getUser());
      $entityManager->persist($figure);
      $entityManager->flush();
      $this->addFlash('success', 'La figure a bien été ajoutée !');
      return $this->redirectToRoute('home_index');
    }
    return $this->render('figure/addFigure.html.twig', [
      'figureForm' => $form->createView(),
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
      $this->denyAccessUnlessGranted('ROLE_USER');
      $user = $this->getUser();
      $now = new DateTime();
      $createdAt = DateTimeImmutable::createFromMutable($now)->setTimezone(new DateTimeZone('UTC'));
      $comment->setCreatedAt($createdAt);
      $comment->setIdPseudo($user);
      $comment->setIdFigure($figure);
      $entityManager->persist($comment);
      $entityManager->flush();
      $this->addFlash('success', 'Votre commentaire a bien été envoyé !');
      return $this->redirectToRoute('home_details', ['slug' => $figure->getSlug()]);

    }



    return $this->render('figure/detail.html.twig', [
      'figure' => $figure,
      'comments' => $comments,
      'commentForm' => $commentForm->createView(),
    ]);
  }

  #[Route('/{slug}/supprimer', name: 'delete')]
  public function deleteFig(Figure $figure, EntityManagerInterface $entityManager): Response{
    $this->denyAccessUnlessGranted('ROLE_USER');
    $entityManager->remove($figure);
    $entityManager->flush();
    $this->addFlash('success', 'La figure a bien été supprimée !');
    return $this->redirectToRoute('home_index');


  }



}

<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Images;
use App\Repository\FigureRepository;
use App\Form\CommentFormType;
use App\Form\FigureFormType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
//use App\Security\Voter\FigureVoter;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

#[Route('/home', name: 'home_')]
/**
 * Summary of FigureController
 */
class FigureController extends AbstractController
{


  #[Route('/', name: 'index')]
  /**
   * Summary of index
   * @param FigureRepository $figureRepository
   * @return Response
   */
  public function index(FigureRepository $figureRepository): Response
  {
    $figures = $figureRepository->findBy([], ['name' => 'asc']);

    $firstImages = [];
    foreach ($figures as $figure) {
      $images = $figure->getImage();
      if ($images) {
        $firstImages[] = $images[0];
      } else {
        $firstImages[] = null;
      }
    }

    return $this->render('figure/index.html.twig', [
      'figures' => $figures,
      'firstImages' => $firstImages,
    ]);
  }

  #[Route('/ajout', name: 'add')]
  /**
   * Summary of addFig
   * @param Request $request
   * @param EntityManagerInterface $entityManager
   * @param SluggerInterface $slugger
   * @return Response
   */
  public function addFig(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
  {

    $this->denyAccessUnlessGranted('ROLE_USER');
    $figure = new Figure;
    $form = $this->createForm(FigureFormType::class, $figure);
    $groupe = $form->get('groupe')->getData();
    $figure->setGroupe($groupe);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      $imageFiles = $form->get('imagesFiles')->getData();
      foreach ($imageFiles as $imageFile) {
        if ($imageFile) {
          $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
          $safeFilename = $slugger->slug($originalFilename);
          $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

          try {
            $imageFile->move(
              $this->getParameter('images_directory'),
              $newFilename
            );
          } catch (FileException $e) {
            //voir plus tard pour erreur exception      
          }

          $images = new Images();
          $images->setSlug($newFilename);
          $figure->addImage($images);
        }
      }

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
  /**
   * Summary of detail
   * @param Figure $figure
   * @param CommentRepository $commentRepository
   * @param Request $request
   * @param EntityManagerInterface $entityManager
   * @return Response
   */
  public function detail(Figure $figure, CommentRepository $commentRepository, Request $request, EntityManagerInterface $entityManager): Response
  {
    $page = $request->query->getInt('page', 1);
    $comments = $commentRepository->findCommentsByFigurePaginated($figure->getId(), $page);
    $firstImage = $figure->getImage()->first();


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
      'firstImage' => $firstImage,
    ]);
  }

  #[Route('/{slug}/supprimer', name: 'delete')]
  /**
   * Summary of deleteFig
   * @param Figure $figure
   * @param EntityManagerInterface $entityManager
   * @return Response
   */
  public function deleteFig(Figure $figure, EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $entityManager->remove($figure);
    $entityManager->flush();
    $this->addFlash('success', 'La figure a bien été supprimée !');
    return $this->redirectToRoute('home_index');
  }
}

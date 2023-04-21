<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Images;
use App\Entity\Videos;
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
    //recupere toutes les figures
    $figures = $figureRepository->findBy([], ['name' => 'asc']);

    $firstImages = [];
    // on parcours le tableau de figures
    foreach ($figures as $figure) {

      // on recupere les images de chaque figure

      $images = $figure->getImage();

      // on recupere la premiere image de chaque figure
      // si il n'y a pas d'image on met null

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
  public function addFig(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, FigureRepository $figureRepository): Response
  {
    // on verifie que l'utilisateur est connecté
    $this->denyAccessUnlessGranted('ROLE_USER');

    // on instancie les entités
    $figure = new Figure;

    $vids = new Videos;

    $Images = new Images;
    
    //on  récupère le formulaire
    $form = $this->createForm(FigureFormType::class, $figure);
    $form->handleRequest($request);

    //on verifie que le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {

      // on verifie que le nom de la figure n'existe pas déjà
      $existingFigure = $figureRepository->findOneBy(['name' => $figure->getName()]);
      //si la figure existe on affiche un message d'erreur
      if ($existingFigure !== null) {
        $this->addFlash('error', 'Une figure du même nom existe déjà.');
        return $this->redirectToRoute('home_add');
      }
      // on recupere les donnees du formulaire (groupes, videos, images)
      $groupe = $form->get('groupe')->getData();
      //on set le groupe de la figure
      $figure->setGroupe($groupe);

      $videos = $form->get('videos')->getData();
      //on set les videos de la figure
      $vids->setUrl($videos);
      $figure->addVideo($vids);


      $imageFiles = $form->get('imagesFiles')->getData();
      //on boucle sur les images
      foreach ($imageFiles as $imageFile) {
        // si il y a une image on la traite
        if ($imageFile) {
          // on recupere le nom de l'image
          $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
          // on la slugifie
          $safeFilename = $slugger->slug($originalFilename);
          // on genere un nom unique
          $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

          // on deplace l'image dans le dossier images
          try {
            $imageFile->move(
              $this->getParameter('images_directory'),
              $newFilename
            );
          } catch (FileException $e) {
            //si il y a une erreur on affiche un message d'erreur
            $this->addFlash('error', 'Une erreur s\'est produite lors de l\'upload de votre image. Réessayez plus tard.');
            return $this->redirectToRoute('home_index');
          }
          // on set l'image
          $images = new Images();
          $images->setSlug($newFilename);
          $figure->addImage($images);
        }
      }
      //on récupère la date du jour pour setter date créa & modif
      $now = new DateTime();
      $createdAt = DateTimeImmutable::createFromMutable($now)->setTimezone(new DateTimeZone('UTC'));
      $figure->setCreatedAt($createdAt);
      $figure->setModifiedAt($createdAt);
      //on set le slug
      $slug = strtolower(str_replace(' ', '-', $figure->getName()));
      $figure->setSlug($slug);
      //on set l'utilisateur
      $figure->setUserId($this->getUser());
      //on persiste et on flush
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
    // on recupere les commentaires de la figure
    // on les pagine
    $page = $request->query->getInt('page', 1);
    $comments = $commentRepository->findCommentsByFigurePaginated($figure->getId(), $page);
    // on recupere la premiere image de la figure
    $firstImage = $figure->getImage()->first();

    // on instancie les entités
    $comment = new Comment;
    // on recupere le formulaire
    $commentForm = $this->createForm(CommentFormType::class, $comment);
    $commentForm->handleRequest($request);
    // on verifie que le formulaire est soumis et valide
    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
      // on verifie que l'utilisateur est connecté
      $this->denyAccessUnlessGranted('ROLE_USER');
      // on recupere l'utilisateur
      $user = $this->getUser();
      // on recupere la date du jour pour setter date créa
      $now = new DateTime();
      $createdAt = DateTimeImmutable::createFromMutable($now)->setTimezone(new DateTimeZone('UTC'));
      // on set la date de création
      $comment->setCreatedAt($createdAt);
      // on set dans comment l'id de l'utilisateur
      $comment->setIdPseudo($user);
      // on set dans comment l'id de la figure
      $comment->setIdFigure($figure);
      // on persiste et on flush
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
    // on verifie que l'utilisateur est connecté
    $this->denyAccessUnlessGranted('ROLE_USER');
    // on supprime la figure
    $entityManager->remove($figure);
    $entityManager->flush();
    $this->addFlash('success', 'La figure a bien été supprimée !');
    return $this->redirectToRoute('home_index');
  }
}

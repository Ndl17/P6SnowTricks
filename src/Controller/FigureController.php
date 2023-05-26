<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\Videos;
use App\Form\CommentFormType;
use App\Form\EditFigureFormType;
use App\Form\FigureFormType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Service\DateTimeProviderService;
use App\Service\ImageCreationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/home', name:'home_')]
/**
 * Summary of FigureController
 */
class FigureController extends AbstractController
{

    #[Route('/', name:'index')]
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

    #[Route('/ajout', name:'add')]
    /**
     * Summary of addFig
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function addFig(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, FigureRepository $figureRepository, DateTimeProviderService $dateTimeProviderService, ImageCreationService $imageService): Response
    {
        // on verifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');

        // on instancie les entités
        $figure = new Figure;
    
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

            foreach ($videos as $video) {
                $vids = new Videos;
                $vids->setUrl($video);
                $figure->addVideo($vids);
            }

            $imageFiles = $form->get('imagesFiles')->getData();

            //on fait appel au service pour ajouter les images
            $imageService->addImage($imageFiles, $figure, $slugger);
            
            //on récupère la date du jour pour setter date créa & modif
            $now = $dateTimeProviderService->getCurrentDateTime();
            $figure->setCreatedAt($now);
            $figure->setModifiedAt($now);
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

    #[Route('/{slug}', name:'details')]
    /**
     * Summary of detail
     * @param Figure $figure
     * @param CommentRepository $commentRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function detail(Figure $figure, CommentRepository $commentRepository, Request $request, EntityManagerInterface $entityManager, DateTimeProviderService $dateTimeProviderService): Response
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

            $now = $dateTimeProviderService->getCurrentDateTime();
            $comment->setCreatedAt($now);

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

    #[Route('/{slug}/supprimer', name:'delete')]
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

        // supprimer toutes les images associées à la figure qui sont upload
        foreach ($figure->getImage() as $image) {
            $imagePath = $this->getParameter('images_directory') . '/' . $image->getSlug();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $entityManager->remove($figure);
        $entityManager->flush();
        $this->addFlash('success', 'La figure a bien été supprimée !');
        return $this->redirectToRoute('home_index');
    }

    /**
     * Summary of editFig
     * @param Figure $figure
     * @param Request $request
     * @return Response
     */
    #[Route('/{slug}/edit', name:'edit')]
    public function editFig($slug, Request $request, FigureRepository $figureRepository, EntityManagerInterface $entityManager, DateTimeProviderService $dateTimeProviderService, SluggerInterface $slugger, ImageCreationService $imageService): Response
    {
        // Récupération de la figure à éditer depuis la base de données
        $figure = $figureRepository->findOneBy(['slug' => $slug]);
        $images = $figure->getImage();
        $firstImage = $figure->getImage()->first();

        // Vérification si la figure existe
        if (!$figure) {
            throw $this->createNotFoundException('Figure non trouvée');
        }

        // Création du formulaire d'édition
        $form = $this->createForm(EditFigureFormType::class, $figure);
        $form->handleRequest($request);
  
        // Vérification si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {

            $existingFigure = $figureRepository->findOneBy(['name' => $figure->getName(), 'id' => $figure->getId()], ['id' => 'DESC']);

            //si une figure avec le même nom existe et que ce n'est pas la figure en cours d'édition, on affiche un message d'erreur
            if ($existingFigure !== null && $existingFigure->getId() !== $figure->getId()) {
                $this->addFlash('error', 'Une figure du même nom existe déjà.');
                return $this->redirectToRoute('home_index');
            }
            // Récupération de l'entité gérant l'upload des images
            $imageFiles = $form->get('image')->getData();

            //on fait appel au service pour ajouter les images
            $imageService->addImage($imageFiles, $figure, $slugger);

            $videos = $form->get('videos')->getData();
            foreach ($videos as $video) {
                $vids = new Videos;
                $vids->setUrl($video);
                $figure->addVideo($vids);
            }
            //on set les videos de la figure

            $now = $dateTimeProviderService->getCurrentDateTime();
            $figure->setModifiedAt($now);
            $figure->setSlug(strtolower(str_replace(' ', '-', $figure->getName())));

            $entityManager->persist($figure);
            $entityManager->flush();

            $this->addFlash('success', 'Figure éditée avec succès');

            return $this->redirectToRoute('home_index');
        }

        // Affichage du formulaire d'édition
        return $this->render('figure/editFigure.html.twig', [
            'figure' => $figure,
            'images' => $images,
            'editForm' => $form->createView(),
            'firstImage' => $firstImage,
        ]);
    }

    }





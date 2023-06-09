<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Service\DateTimeProviderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CommentCreationService
{
    private $entityManager;
    private $dateTimeProviderService;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, DateTimeProviderService $dateTimeProviderService, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->dateTimeProviderService = $dateTimeProviderService;
        $this->security = $security;
    }

    public function createComment(string $content, Figure $figure): Comment
    {
        $comment = new Comment;
        $user = $this->security->getUser();
        $now = $this->dateTimeProviderService->getCurrentDateTime();
        $comment->setContent($content);
        $comment->setCreatedAt($now);
        $comment->setUser($user);
        $comment->setFigure($figure);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }
}

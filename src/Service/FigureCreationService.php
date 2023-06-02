<?php

namespace App\Service;

use App\Entity\Figure;
use App\Service\DateTimeProviderService;
use \Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureCreationService
{
    private $dateTimeProviderService;
    private $slugger;
    private $security;

    public function __construct(DateTimeProviderService $dateTimeProviderService, SluggerInterface $slugger, Security $security)
    {
        $this->dateTimeProviderService = $dateTimeProviderService;
        $this->slugger = $slugger;
        $this->security = $security;
    }

    public function setFigureDetails(Figure $figure, $isNew = false): void
    {
        $now = $this->dateTimeProviderService->getCurrentDateTime();
        $figure->setModifiedAt($now);

        // Set created date only for new Figure
        if ($isNew) {
            $figure->setCreatedAt($now);
            $figure->setUser($this->security->getUser());  // Set user id for new Figure
        }

        // Slug generation
        $figure->setSlug($this->slugger->slug(strtolower($figure->getName()))->toString());
    }
}

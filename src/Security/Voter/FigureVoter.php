<?php

namespace App\Security\Voter;

use App\Entity\Figure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
*
*/
class FigureVoter extends Voter
{

  const EDIT = 'FIGURE_EDIT';
  const DELETE = 'FIGURE_DELETE';
  const COMMENT = 'FIGURE_COMMENT';
  private $security;


  public  function __construct(Security $security)
  {
    $this->security = $security;
  }

  protected function supports(string $attribute, mixed  $figure):bool{
    if (!in_array($attribute, [self::EDIT, self::DELETE, self::COMMENT])) {
      return false;
    }
    if (!$figure instanceof Figure) {
      return false;
    }
    return true;

  }

  protected function voteOnAttribute(string $attribute, mixed  $figure, TokenInterface $token):bool{
    $user =  $token->getUser();
    if (!$user instanceof UserInterface) {
      return false;
    }
    if ($this->security->isGranted('ROLE_USER')) {
      return true;
    }
    return false;
  }
}

<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class CommentFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
    ->add('content', TextType::class, [
      'label' => 'Ajout de commentaire',
      'attr' => ['class'=>'form-control'],
    ])
    ->add('created_at', HiddenType::class, [
      'mapped' => false,
    ])
    ->add('idPseudo', HiddenType::class, [
      'mapped' => false,
    ])
    ->add('idPseudo', HiddenType::class, [
      'mapped' => false,
    ])
    ->add('Envoyer', SubmitType::class ,['attr' => ['class'=>'btn btn-primary'],
])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Comment::class,
    ]);
  }
}

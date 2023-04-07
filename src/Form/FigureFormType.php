<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class FigureFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
    ->add('name')
    ->add('description')
    ->add('type')
    ->add('imgName', HiddenType::class, [
      'mapped' => false,
    ])
    ->add('vidName', HiddenType::class, [
      'mapped' => false,
    ])
    ->add('created_at', HiddenType::class, [
      'mapped' => false,
    ])
    ->add('modified_at', HiddenType::class, [
      'mapped' => false,
    ])
    ->add('slug', HiddenType::class, [
      'mapped' => false,
    ])
    ->add('userId', HiddenType::class, [
      'mapped' => false,
    ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Figure::class,
    ]);
  }
}
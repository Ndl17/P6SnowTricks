<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Groupe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class FigureFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('name', TextType::class, [
        'label' => 'Nom de la figure',
        'attr' => ['class' => 'form-control'],
      ])
      ->add('description', TextareaType::class, [
        'label' => 'Décrivez votre figure',
        'attr' => ['class' => 'form-control'],
      ])

    ->add('imagesFiles', FileType::class, [
      'label' => 'Uploadez une image',
      'mapped' => false,
      'multiple' => true,
      'attr' => ['class' => 'form-control'],
      'constraints' => [
        new All([
          'constraints' => [
            new File([
              //'maxSize' => '1024k',
             'mimeTypesMessage' => 'Veuillez uploader un fichier au format jpeg ou png',
              'mimeTypes' => [
                        'image/jpeg',
                'image/png',
              ]
            ]),
          ],
        ]),
      ]
    ])

->add('groupe', EntityType::class, [
    'class' => Groupe::class,
    'choice_label' => 'name',
    'label' => 'Groupe de figure',
    'attr' => ['class'=>'form-control'],
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
      ->add('Envoyer', SubmitType::class, [
        'attr' => ['class' => 'btn btn-primary'],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Figure::class,
    ]);
  }
}

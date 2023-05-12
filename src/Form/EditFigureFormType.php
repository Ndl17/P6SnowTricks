<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Groupe;
use App\Entity\Videos;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditFigureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la figure',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Décrivez votre figure',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('groupe', EntityType::class, [
                'class' => Groupe::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
            ])

            ->add('imagesFiles', FileType::class, [
                'label' => 'Uploadez une image',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '1024k',
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

    ->add('videos', CollectionType::class, [
      'entry_type' => UrlType::class,
      'label' => 'Vidéos',
      'required' => false,
      'mapped' => false,
      'allow_add' => true,
      'allow_delete' => true,
      'attr' => ['class' => 'form-control'],
      'entry_options' => [
        'attr' => ['class' => 'form-control'],
      ],
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

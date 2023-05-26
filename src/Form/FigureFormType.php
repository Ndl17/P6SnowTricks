<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Groupe;
use PHPUnit\TextUI\Help;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class FigureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la figure',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Décrivez votre figure',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])

            ->add('imagesFiles', FileType::class, [
                'label' => 'Uploadez une image',
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '3072k',
                                'mimeTypes' => [
                                    'image/*',
                                ],
                                'mimeTypesMessage' => 'Veuillez uploader un fichier au bon format',
                            ]),
                        ],
                    ]),
                ],
            ])

            ->add('groupe', EntityType::class, [
                'class' => Groupe::class,
                'choice_label' => 'name',
                'label' => 'Groupe de figure',
                'attr' => ['class' => 'form-control'],
            ])

            ->add('videos', CollectionType::class, [
                'entry_type' => UrlType::class,
                'label' => 'Insérez une vidéo',
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

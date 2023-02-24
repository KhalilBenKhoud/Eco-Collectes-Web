<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Annonces;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnnoncesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please set a valid title',
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => false,
            ])
            ->add('imgUrl', FileType::class, 
            [
                'invalid_message' => 'Fichier Invalide',
                'attr' => [ 'class' => 'form-control'],
                'required' => false,
                'label' => 'Image',
                'data_class' => null,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ]),
                ],
            ])
        ;
    }

    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}

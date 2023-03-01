<?php

namespace App\Form;

use App\Entity\Article;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Console\Descriptor\TextDescriptor;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('contenu', TextareaType::class)
            ->add('photo', FileType::class, [
                'mapped' => false,
                'label' => 'photo',
                'required' => false,
            ])
            ->add('categorie')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
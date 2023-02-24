<?php

namespace App\Form;

use App\Entity\Contrat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description',TextareaType::class)
            ->add('date_debut',DateType::class,[
                'input'=>'datetime',
                'widget'=>'single_text'
            ])
            ->add('date_fin',DateType::class,[
                'input'=>'datetime',
                'widget'=>'single_text'
            ])
            //->add('statut_contrat')
            ->add('type_contrat',TextType::class)
            ->add('montant',NumberType::class)
          /*  ->add('enterprise',EntityType::class,[
                'class'=>\App\Entity\Entreprise::class,
                'choice_label'=>'nom'
            ])
            ->add('collecteur',EntityType::class,[
                'class'=>\App\Entity\User::class,
                'choice_label'=>'email'
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Invitation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('date_invitation')
            ->add('statut_invitation')
            ->add('contrat',EntityType::class,[
                'class'=>\App\Entity\Contrat::class,
                'choice_label'=>'description'
            ])
           /* ->add('collecteur',EntityType::class,[
                'class'=>\App\Entity\User::class,
                'choice_label'=>'email'
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invitation::class,
        ]);
    }
}

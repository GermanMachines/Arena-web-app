<?php

namespace App\Form;

use App\Entity\Tournois;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TournoisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('descriptiontournois')
            ->add('type')
            ->add('nbrparticipants')
            ->add('idjeux')
            ->add('winner')
            ->add('status')
            ->add('idequipe')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournois::class,
        ]);
    }
}

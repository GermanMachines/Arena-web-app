<?php

namespace App\Form;

use App\Entity\Tournois;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('winner')
            ->add('status')
            ->add('idjeux' , EntityType::class, array(
                'class' => 'App\Entity\Jeux',
                'choice_label' => function ($Jeux){ 
                   
                    return $Jeux->getNomjeux();}
            ))
            //->add('idequipe')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournois::class,
        ]);
    }
}

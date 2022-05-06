<?php

namespace App\Form;

use App\Entity\MatchEq;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class MatchEqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           # ->add('idmatch' , EntityType::class, array(
             #    'class' => 'App\Entity\Matchs',
             #    'choice_label' => function ($Match){ 
                
               #      return $Match->getIdmatch();}
             #))
            # ->add('idequipe' , EntityType::class, array(
             #    'class' => 'App\Entity\Equipe',
             #    'choice_label' => function ($Equipe){ 
                   
                #     return $Equipe->getNom();}
            # ))
            ->add('idmatch')
            ->add('idequipe')
            ->add('score')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MatchEq::class,
        ]);
    }
}

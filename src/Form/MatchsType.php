<?php

namespace App\Form;

use App\Entity\Matchs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class MatchsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datematch')
            ->add('reference')
            ->add('idtournois' , EntityType::class, array(
                'class' => 'App\Entity\Tournois',
                'choice_label' => function ($Tournois){ 
                   
                    return $Tournois->getTitre();}
            ))
            ->add('idequipe')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matchs::class,
        ]);
    }
}

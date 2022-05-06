<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\User;
use App\Entity\Jeux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('score')
            ->add('commentaire')
            ->add('idjeux', EntityType::class, array(
                'label' => 'jeux',
                'class' => 'App\Entity\Jeux',
                'choice_label' => function ($jeux) {

                    return $jeux->getNomjeux();
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}

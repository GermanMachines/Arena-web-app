<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Reclamation;


class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('message')
            ->add('idUser', EntityType::class, array(
                'class' => 'App\Entity\User',
                'choice_label' => function ($user) {

                    return $user->getNom();
                }
            ))
            ->add('idcategoryreclamation', EntityType::class, array(
                'class' => 'App\Entity\Categoryreclamation',
                'choice_label' => function ($cat) {

                    return $cat->getNom();
                }
            ))
            ->add('etat');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}

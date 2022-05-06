<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Reclamation;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ReclamationFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('message')
            ->add('idcategoryreclamation', EntityType::class, array(
                'label' => 'category reclamation',
                'class' => 'App\Entity\Categoryreclamation',
                'choice_label' => function ($cat) {

                    return $cat->getNom();
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}

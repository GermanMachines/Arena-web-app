<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('surnom')
            ->add('nom')
            ->add('image', FileType::class , [ 'mapped'=> false])
            ->add('email',EmailType::class)
            ->add('mdp',PasswordType::class)
            ->add('confirmpassword', PasswordType::class)
            ->add('telephone')
            ->add('role',ChoiceType::class,[
                'choices'=>
                    [
                        'client'=>'[ROLE_USER]',
                        'admin'=>'[ROLE_ADMIN]'
                    ]
            ])
            ->add('block',ChoiceType::class,[
                'choices'=>
                    [
                        'non'=>'non',
                        'oui'=>'oui'
                    ]
            ])
            ->add('idEquipe',EntityType::class,[
                'class'=>Equipe::class,
                'choice_label'=>'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

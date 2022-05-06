<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('surnom')
            ->add('nom')
            ->add('image')
            ->add('email',EmailType::class)
            ->add('mdp',PasswordType::class)
            ->add('confirmpassword',PasswordType::class)
            ->add('telephone')
            /*->add('captchaCode', CaptchaType::class, array(
                'captchaConfig' => 'ExampleCaptchaUserRegistration',
                'constraints' => [
                   new ValidCaptcha ([
                        'message' => 'Invalid captcha, please try again',
                    ]),
                ],
            ))*/
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

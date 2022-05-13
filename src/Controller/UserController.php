<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionType;
use App\Form\ResetpassType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Twilio\Rest\Client;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_user")
     */
    public function index(UserRepository $repository): Response
    {
        $users = $repository->findAll();
        return $this->render('user/index.html.twig', [
            'users'=>$users
        ]);
    }

    /**
     * @Route("/user/ajouter", name="ajouter_user")
     * @Route("/user/modifier/{id}", name="modifier_user")
     */
    public function Ajouter_modifier(User $user = null, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $modif = false;
        if (!$user) {
            $user = new User();
            $modif = true;
        }
        $form = $this->createForm(UserType::class, $user);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $file = $request->files->get('user')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $user->setImage($filename);


            $passwordcrypt = $encoder->encodePassword($user, $user->getMdp());
            $user->setMdp($passwordcrypt);
            $user->setBlock('non');
            $user->setRole('ROLE_JOUEUR');
            $user->setRoles(['ROLE_JOUEUR']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', "L'action a ete effectué");
            return $this->redirectToRoute('admin_user');
        }
        return $this->render('user/New_modif_user.html.twig', [
            "form" => $form->createView(),
            "modif" => $modif
        ]);
    }

    /**
     * @Route("/user/supprimer/{id}", name="supprimer_user")
     */
    public function delete(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', "L'action a ete effectué");
        return $this->redirectToRoute('admin_user');
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function Inscription(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $utilisateur = new User();
        $form = $this->createForm(InscriptionType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           /* $file = $request->files->get('user')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );*/
            $passwordcrypt = $encoder->encodePassword($utilisateur, $utilisateur->getMdp());
            $utilisateur->setMdp($passwordcrypt);
            $utilisateur->setBlock('non');
            $utilisateur->setRole('ROLE_JOUEUR');
            $utilisateur->setRoles('[ROLE_JOUEUR]');
            $utilisateur->setPassword($passwordcrypt);
            $em = $this->getDoctrine()->getManager();
            $em->persist($utilisateur);
            $em->flush();
            $this->addFlash('success', "L'action a ete effectué");
            return $this->redirectToRoute('global');
        }
        return $this->render('user/Inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/login", name="login")
     *
     */
    public function login(AuthenticationUtils $utils): Response
    {
        return $this->render('user/login.html.twig', [
            'LastUserName' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError()
        ]);
    }


    /**
     * @Route("/logout", name="logout")
     *
     */
    public function logout()
    {

    }

    /**
     * @Route("/passoublie", name="motdepassoublie")
     */
    public function motdepassoublie(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, UserRepository $repository): Response
    {
        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($request);
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $charactersLength; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        if ($form->isSubmitted() && $form->isValid())
        {
            $donnees = $form->getData();
            $user = $repository->findOneByEmail($donnees['email']);
            if (!$user) {
                $this->addFlash('success', "adresse n'existe pas");
                $this->redirectToRoute('login');
            }
            $em = $this->getDoctrine()->getManager();
            $passwordcrypt = $encoder->encodePassword($user , $randomString);
            $user->setPassword($passwordcrypt);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', "L'action a ete effectué");
            $email = (new \Swift_Message('Reinitialisation de compte:' . $user->getNom()))
               // ->setFrom('mohammedmohsen.khefacha@esprit.tn')
               // ->setFrom('mohamedaziz.sahnoun@esprit.tn')
               ->setFrom('nour.boujmil@esprit.tn')
                ->setTo($user->getEmail())
                ->setBody($this->render('emails/tousermotdepass.html.twig', [
                        'user' => $user,
                        'randomstring'=>$randomString
                    ]
                ), 'text/html'

                );
            $mailer->send($email);
            $this->addFlash('message', 'le message a ete bien envoye');
            return $this->redirectToRoute('login');
        }
        return $this->render('user/forgotten_pass.html.twig',[
            'emailform'=>$form->createView()
        ]);
    }

    /**
     * @Route("/{id}/block/", name="user_block", methods={"GET", "POST"})
     */
    public function bloquer(  $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);


        $user->setBlock('oui');
        $entityManager->flush();

        $sid = "ACf920c722af1c2207c54355e3b18da3ee"; // Your Account SID from www.twilio.com/console
        $token = "55885ea3ce08dc21be9da7f12d70ca48"; // Your Auth Token from www.twilio.com/console

        $twilio_number = "+19705175489";

        $client = new Client($sid, $token);
        $client->messages->create(
        // Where to send a text message (your cell phone?)
            '+216'.$user->getTelephone(),
            array(
                'from' => $twilio_number,
                'body' => 'vous avez été bloqué'
            ));

        return $this->redirectToRoute('admin_user', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/front/afficheuser", name="afficheuser")
     */
    public function AfficherUserF (UserRepository $repository, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        {
            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();

            $users = $repository->findAll();
            $user1 = $this->get('security.token_storage')->getToken()->getUser();

            return $this->render('user/afficheruser.html.twig', [
                'u' => $users]);

        }

    }

   /**
   * @Route("/updatefront/{id}",name="m")
    */
   function modifier(UserRepository $repository, $id, Request $request, \Swift_Mailer $mailer,UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $user = $repository->find($id);
        $form = $this->createForm(InscriptionType::class, $user);
        $form->handleRequest($request);
        $p=$form->get('mdp')->getData();
        $p=$userPasswordEncoder->encodePassword($user,$p);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("afficheruser");
        }
        return $this->render("user/updateuser.html.twig", ['f' => $form->createView()]);

    }






/**
     * @Route("/AllUsers",name="AllUsers")
     */
    public function AllUsers(NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        
        

        $jsonContent = $Normalizer->normalize( $users,  'json', ['groups' => 'post:read']);
        dump($jsonContent);
        return new Response(json_encode($jsonContent));
    }

     /**
     * @Route("/codename/DeleteUser/{id}",name="deleteuser")
     */
    public function deleteUserId(Request $request, NormalizerInterface $Normalizer, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'post:read']);
        return new Response("User Deleted successfully" . json_encode($jsonContent));
    }

    // /**
   //  * @Route("/AddUserr",name="ad")
   //  */
    /*function AddUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, NormalizerInterface $Normalizer, \Swift_Mailer $mailer)
    { $randomString = 55484;
        $user = new User();
        $session = $request->getSession();
        $user->setNom($request->get('nom'));
        $user->setSurnom($request->get('surnom'));
        $user->setImage($request->get('image'));
        $user->setEmail($request->get('email'));
        $user->setMdp(
            $passwordEncoder->encodePassword(
                $user,
                $request->get('mdp')
            )
        );

        $user->setTelephone($request->get('telephone'));
       // $user->setIdEquipe($request->get("idequipe"));
        
        
        $user->setRole($request->get('role'));
        $user->setBlock($request->get('block'));
        $user->setRoles($request->get('roles'));
        $user->setUsername($request->get('username'));
       

       
        $em = $this->getDoctrine()->getManager();
        $session->set('ok', $user->getPassword());
        $em->persist($user);
        $em->flush();
        $email = (new \Swift_Message('Inscription:' . $user->getNom()))
        // ->setFrom('mohammedmohsen.khefacha@esprit.tn')
        // ->setFrom('mohamedaziz.sahnoun@esprit.tn')
        ->setFrom('nour.boujmil@esprit.tn')
         ->setTo($user->getEmail())
         ->setBody($this->render('emails/tousermotdepass.html.twig', [
            'user' => $user,
            'randomstring'=>$randomString
        ]
    ), 'text/html'

    );
     $mailer->send($email);
        return new Response("user added succ");


    }

    */

    /**
     * @Route("/AddUserr",name="ad")
     */
    function AddUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, NormalizerInterface $Normalizer, \Swift_Mailer $mailer)
    { $randomString = 55484;
        $user = new User();
        $session = $request->getSession();
        $user->setNom($request->get('nom'));
        $user->setSurnom($request->get('surnom'));
        $user->setImage($request->get('image'));
        $user->setEmail($request->get('email'));
        $user->setMdp(
            $passwordEncoder->encodePassword(
                $user,
                $request->get('mdp')
            )
        );

        $user->setTelephone($request->get('telephone'));
       // $user->setIdEquipe($request->get("idequipe"));
        
        
        $user->setRole($request->get('role'));
        $user->setBlock($request->get('block'));
        $user->setRoles($request->get('roles'));
        $user->setUsername($request->get('username'));
       

       
        $em = $this->getDoctrine()->getManager();
        $session->set('ok', $user->getPassword());
        $em->persist($user);
        $em->flush();
        $email = (new \Swift_Message('Inscription:' . $user->getNom()))
        // ->setFrom('mohammedmohsen.khefacha@esprit.tn')
        // ->setFrom('mohamedaziz.sahnoun@esprit.tn')
        ->setFrom('nour.boujmil@esprit.tn')
         ->setTo($user->getEmail())
         ->setBody($this->render('emails/tousermotdepass.html.twig', [
            'user' => $user,
            'randomstring'=>$randomString
        ]
    ), 'text/html'

    );
     $mailer->send($email);

     $sid = "ACf920c722af1c2207c54355e3b18da3ee"; // Your Account SID from www.twilio.com/console
     $token = "55885ea3ce08dc21be9da7f12d70ca48"; // Your Auth Token from www.twilio.com/console

     $twilio_number = "+19705175489";

     $client = new Client($sid, $token);
     $client->messages->create(
     // Where to send a text message (your cell phone?)
         '+216'.$user->getTelephone(),
         array(
             'from' => $twilio_number,
             'body' => 'vous avez été enregisté sur Arena Mobile'
         ));

        return new Response("user added succ");


    }





     /**
     * @Route("/User/{id}",name="Users")
     */
    public function UserId(Request $request, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));
    }



    /**
     * @Route("/auth",name="auth")
     */
    public function auth(Request $request, NormalizerInterface $Normalizer, UserPasswordEncoderInterface $passwordEncoder, UserRepository $rep)
    { $user = new User();
        $test = false ;
        $em = $this->getDoctrine()->getManager();
        $user = $rep->findByUsername($request->get('username'));
     //   $nom = $user->getEquipe()->__toString();
       $x = $request->get('password');

       
     // echo $user->getPassword();
      foreach ($user as $y){
       // echo $y->getPassword();
        if ( $x == $y->getPassword() ) { $test = true; }
    }
  
     
        
       
 // echo $test;
       if ($test) {
        $jsonContent = $Normalizer->normalize($user,  'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));
   } else {
       return new Response("not connected");
    }

    
   
       // $test = $passwordEncoder->isPasswordValid($user, $request->get('password'));
       


    }

    /**
     * @Route("/UpdateUser/{id}",name="kavi")
     */
    public function UpdateUserId(Request $request, NormalizerInterface $Normalizer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($request->get('id'));
        $user->setNom($request->get('nom'));
        $user->setSurnom($request->get('surnom'));
        $user->setImage($request->get('image'));
        $user->setEmail($request->get('email'));
        $user->setTelephone($request->get('telephone'));
        // $user->setIdEquipe($request->get("idequipe"));
         
         
         $user->setRole($request->get('role'));
         $user->setBlock($request->get('block'));
         $user->setRoles($request->get('roles'));
         $user->setUsername($request->get('username'));
        
 
        $em->flush();
        $jsonContent = $Normalizer->normalize($user, 'json', ['groups' => 'post:read']);
        dump($jsonContent);
        return new Response("Information updated successfully" . json_encode($jsonContent));
    }

















}

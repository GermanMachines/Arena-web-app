<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;   
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
  protected $session;
  public function __construct(SessionInterface $session)
  {
    $this->session = $session;
  }
    /**
     * @Route("/index", name="app_user_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request,  EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="app_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, $id ,  EntityManagerInterface $entityManager): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/block", name="app_user_block", methods={"GET", "POST"})
     */
    public function bloquer(  $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);


            $user->setBlock('oui');
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

     /**
     * @Route("/searchEmail/{email}", name="searchEmail")
     */
    public function searchByEmail($email,UserRepository $repository){
        $user =$repository->findOneBy(['email'=>$email]);
        if($user){
            return $this->render('user/recuperation.html.twig', [
                'user' =>$user,
            ]);
        }
        else{
                // $Message= new Message(NULL,'');
        }
    }

    /**
     * @Route("/sms/{telephone}", name="sms")
     */
    public function envoyerSms($telephone){
        $rand=mt_rand(10000, 99999);
        echo $rand;
        $sid = "ACf920c722af1c2207c54355e3b18da3ee"; // Your Account SID from www.twilio.com/console
        $token = "55885ea3ce08dc21be9da7f12d70ca48"; // Your Auth Token from www.twilio.com/console

        $twilio_number = "+19705175489";

        $client = new Client($sid, $token);
        $client->messages->create(
        // Where to send a text message (your cell phone?)
            '+216'.$telephone,
            array(
                'from' => $twilio_number,
                'body' => 'I sent this message in under 10 minutes!'.$rand
            ));

    }

    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface  $em, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form  = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
           $passwordcrypt = $encoder->encodePassword($user, $user->getPassword());
           $user->setMdp($passwordcrypt);
            //l'objet $em sera affecté automatiquement grâce à l'injection des dépendances de symfony 4
           $user->setRole("joueur");
           $user->setBlock("non");
           $user->setIdEquipe(NULL);
           $em->persist($user);
           $em->flush();
           return $this->redirectToRoute('login');
        }
       return $this->render('user/registration.html.twig',
                           ['form' =>$form->createView()]);
    }

     /**
     * @Route("/login", name="login")
     *
     */

    public function login(AuthenticationUtils $utils): Response
    {
        return $this->render('security/login.html.twig', [
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

 //   /**
 //  * @Route("/signin", name="signin")
 //  */
/*  public function signin(Request $request): Response
  {
    $username = $request->request->get("username");
    $password = $request->request->get("mdp");
    $input = [
      'username' => $username,
      'mdp' => $password,
    ];



    $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
      'username' => $username,
    ]);
    if (is_null($user)||password_verify($password, $user->getMdp())==false) {
      return $this->render('/security/login.html.twig',['error'=>"username or mot de passe not valid", 'msg' => $user->getMdp() ]);
    }
    else {
      $this->session->set('user', $user);
      if ($user->getRole() == "admin") {
        return $this->redirectToRoute('app_user_index');
      } elseif ($user->getRole() == "joueur") {
        return $this->redirectToRoute('security_registration');
      }
    }
    return $this->render('/security/login.html.twig',['msg' => $user->getRole()]);
  }

*/





}

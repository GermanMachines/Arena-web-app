<?php

namespace App\Controller;

use App\Entity\EmailReclamation;
use App\Entity\Reclamation;
use App\Entity\User;
use App\Form\EreclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class EmailReclamationController extends AbstractController
{
    /**
     * @Route("/email/reclamation/{id}", name="app_email_reclamation")
     */
    public function index(EntityManagerInterface $entityManager, Request $request, Reclamation $reclamation, \Swift_Mailer $mailer): Response
    {

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $userId = $reclamation->getIduser();
        $user = $userRepository->find($userId);




        $emailRec = new EmailReclamation();
        $emailRec = ["reciever" => $user->getEmail()];
        $form = $this->createForm(EreclamationType::class, $emailRec);


        $form->handleRequest($request);
        $this->addFlash('info', 'Responding will result in changing the state of this reclamation');
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $message = (new \Swift_Message('Arena Adminstration'))
                ->setFrom('abdousfayhitest@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $data['content']
                )
                ->setSubject($data['subject']);
            $mailer->send($message);

            //change etat
            $em = $this->getDoctrine()->getManager();
            $reclamation->setEtat(true);
            $em->persist($reclamation);
            $em->flush();

            $this->addFlash('success', 'Email sent successfully');

            // }
            // return $this->render('/reclamation/email.html.twig', [
            //     'form' => $form->createView()
            // ]);
        }
        return $this->render('reclamation/email.html.twig', [
            'form' => $form->createView()
        ]);
        // /**
        //  * @Route("/respond" name="app_email_respond")
        //  */
        // public function respond(Request $request, \Swift_Mailer $mailer)
        // {
        //     $transport = Transport::fromDsn('smtp://localhost');
        //     $mailer = new Mailer($transport);

        //     $email = (new Email())
        //         ->from('abdousfayhitest@gmail.com')
        //         ->to('abdousfayhi12@gmail.com')
        //         //->cc('cc@example.com')
        //         //->bcc('bcc@example.com')
        //         //->replyTo('fabien@example.com')
        //         //->priority(Email::PRIORITY_HIGH)
        //         ->subject('Time for Symfony Mailer!')
        //         ->text('Sending emails is fun again!')
        //         ->html('<p>See Twig integration for better HTML integration!</p>');

        //     $mailer->send($email);

        //     $message = (new \Swift_Message('Hello Email'))
        //         ->setFrom('abdousfayhitest@gmail.com')
        //         ->setTo('abdousfayhi12@gmail.com')
        //         ->setBody(
        //             'aaaaaaa text via mail',
        //             'text/plain'
        //         );



        //     // $mailer->send($message);
        //     return new Response('sent');
        // }
    }
}

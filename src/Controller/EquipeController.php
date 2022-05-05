<?php

namespace App\Controller;
use App\Entity\Tournois;

use App\Entity\Equipe;
use App\Form\EquipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ParticipationRepository;
use App\Repository\EquipeRepository;
use App\Repository\TournoisRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use App\Services\QrcodeService;



/**
 * @Route("/equipe")
 */
class EquipeController extends AbstractController
{



    private $flashMessage;

    public function __construct(
    
        FlashBagInterface $flashMessage
    ){
        $this->flashMessage = $flashMessage;
    }


    /**
     * @Route("/", name="app_equipe_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $equipes = $entityManager
            ->getRepository(Equipe::class)
            ->findAll();

        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipes,
        ]);
    }

    /**
     * @Route("/new", name="app_equipe_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipe);
            $entityManager->flush();

            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idequipe}", name="app_equipe_show", methods={"GET"})
     */
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    /**
     * @Route("/{idequipe}/edit", name="app_equipe_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idequipe}", name="app_equipe_delete", methods={"POST"})
     */
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getIdequipe(), $request->request->get('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
    }





     /**
     * @Route("/addparticipation/{id}", name="add_participation_tournois")
     * @param Request $request
     * @param QrcodeService $qrcodeService
     * @return Response
     */      
    public function addparticipation(MailerInterface $mailer,Request $request,$id,TournoisRepository $repository , QrcodeService $qrcodeService): Response
    {
        $qrCode = null;

        $data = "";
        $qrCode = $qrcodeService->qrcode($data);
    
        $Equipe = new Equipe();
        $Tournois = new Tournois();
        $Tournois = $this->getDoctrine()->getRepository(Tournois::class)->find($id);
        $Equipe = $this->getDoctrine()->getRepository(Equipe::class)->find(94);
       $Equipe->addIdtournoi($Tournois);

        $counter=sizeof($Tournois->getIdequipe());

        $Disponible=$Tournois->getStatus();

        $nbr=$Tournois->getNbrparticipants();
       // $TournoisCount= $repository->countParticipants($id);
          if($counter<$nbr && $Disponible=="Disponible"){
            //envoie email
                    $email = (new TemplatedEmail())
                    ->from('Arena@estrit.com')
                    ->to('tarek.ayadi@esprit.tn')
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject('Time for Symfony Mailer!')
                    ->htmlTemplate('email/welcome.html.twig')
                    ->context([
                        'Tournois' => $Tournois,
                        'Equipe' => $Equipe,
                        'qrCode' => $qrCode

                    ]);

            $mailer->send($email);


            //envoie email end




        $em = $this->getDoctrine()->getManager();
        $em->persist($Equipe);
        $em->flush();
        return $this->redirectToRoute("app_participation_indexq");
        }
                else if($counter>$nbr){
                    $this->flashMessage->add("warning","You can't participate tournament is FULL !");
                    return $this->redirectToRoute("app_tournois_indexfront");
                }
                else{
                    $this->flashMessage->add("warning","You can't participate tournament Status : not disponible !");
                    return $this->redirectToRoute("app_tournois_indexfront"); 
                }


    }
    

    /**
     * @Route("/front/new/mylist", name="app_participation_indexq", methods={"GET"})
     */
    public function indexq(EntityManagerInterface $entityManager): Response
    {
        {
            $equipes = $entityManager
                ->getRepository(Equipe::class)
                ->findBy(array('idequipe' => '94'));
    
                return $this->render('participation/indexfront.html.twig', [
                    'equipes' => $equipes,
                ]);
    }

}

  


}
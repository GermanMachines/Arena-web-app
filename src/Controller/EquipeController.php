<?php

namespace App\Controller;
use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Tournois;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipationRepository;
use App\Repository\TournoisRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use App\Services\QrcodeService;




use Symfony\Component\HttpFoundation\Request; // Nous avons besoin d'accéder à la requête pour obtenir le numéro de page
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

class EquipeController extends AbstractController

{


    private $flashMessage;

public function __construct(

    FlashBagInterface $flashMessage
){
    $this->flashMessage = $flashMessage;
}


    /**
     * @Route("/admin/equipe", name="admin_equipe")
     */
    public function index(EquipeRepository $repository,Request $request, PaginatorInterface $paginator): Response
    {    $equipe = $repository->findAll();
        $equipes = $paginator->paginate(
         $equipe,
        $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
        2// Nombre de résultats par page
    );
       // $session = $request->getSession();
        $user1 = $this->get('security.token_storage')->getToken()->getUser();
        if($user1 instanceof User)
      echo $user1->getNom();
        return $this->render('equipe/index.html.twig', [
            'equipes'=>$equipes
        ]);
    }
    /**
     * @Route("/equipe/ajouter", name="ajouter_equipe")
     * @Route("/equipe/modifier/{id}", name="modifier_equipe")
     */
    public function Ajouter_modifier(Equipe $equipe = null, Request $request)
    {
        $modif = false;
        if (!$equipe) {
            $equipe = new Equipe();
            $modif = true;
        }
        $form = $this->createForm(EquipeType::class, $equipe);
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('equipe')['logo'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $equipe->setLogo($filename);
            $em = $this->getDoctrine()->getManager();
            $em->persist($equipe);
            $em->flush();
            $this->addFlash('success', "L'action a ete effectué");
            return $this->redirectToRoute('admin_equipe');
        }
        return $this->render('equipe/New_modif_equipe.html.twig', [
            "form" => $form->createView(),
            "modif" => $modif
        ]);
    }

    /**
     * @Route("/front/equipe", name="front_equipe")
     */
    public function index1(EquipeRepository $repository): Response
    {
        $equipes = $repository->findAll();
        return $this->render('equipe/indexfront.html.twig', [
            'equipes1'=>$equipes
        ]);
    }

    /**
     * @Route("stats", name="stat")
     */
    public function new2(): Response
    { $repository = $this->getDoctrine()->getRepository(Equipe::class);
        $equipe = $repository->findAll();


        $rd=0;
        $qu=0;
        $es=0;


        foreach ($equipe as $equipe)
        {
            if (  $equipe->getScore()<23)  :

                $rd+=1;
            elseif ($equipe->getScore()>23):

                $qu+=1;


            endif;

        }


        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['Type', 'nombres'],
                ['Score<23',     $rd],
                ['Score>23',      $qu]

            ]
        );
        $pieChart->getOptions()->setColors(['#ffd700', '#C0C0C0', '#cd7f32']);

        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

        return $this->render('equipe/stat.html.twig', array('piechart' => $pieChart));

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


        $user1 = $this->get('security.token_storage')->getToken()->getUser();


        $Equipe = $this->getDoctrine()->getRepository(Equipe::class)->find($user1->getIdEquipe());
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
                    $user1 = $this->get('security.token_storage')->getToken()->getUser();

            $equipes = $entityManager
                ->getRepository(Equipe::class)
                ->findBy(array('idequipe' => $user1->getIdEquipe()));
    
                return $this->render('participation/indexfront.html.twig', [
                    'equipes' => $equipes,
                ]);
    }

}




}

<?php

namespace App\Controller;

use App\Entity\Tournois;
use App\Form\TournoisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ParticipationRepository;
use App\Entity\Equipe;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Jeux;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Repository\TournoisRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Repository\UserRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * @Route("/tournois")
 */
class TournoisController extends AbstractController
{
    /**
     * @Route("/", name="app_tournois_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tournois = $entityManager
            ->getRepository(Tournois::class)
            ->findAll();

        return $this->render('tournois/index.html.twig', [
            'tournois' => $tournois,
        ]);
    }


    
    /**
     * @Route("/front", name="app_tournois_indexfront", methods={"GET"})
     */
    public function indexfront(EntityManagerInterface $entityManager): Response
    {
        $tournois = $entityManager
            ->getRepository(Tournois::class)
            ->findAll();

            $counter=[];
            foreach($tournois as $t){
            $counter[]=sizeof($t->getIdequipe());
            }

            $user1 = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('tournois/indexfront.html.twig', [
            'tournois' => $tournois,
            'counter'=>$counter,
            'u' => $user1,
        ]);
    }

    /**
     * @Route("/new", name="app_tournois_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tournoi = new Tournois();
        $form = $this->createForm(TournoisType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tournoi);
            $entityManager->flush();

            return $this->redirectToRoute('app_tournois_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournois/new.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idtournois}", name="app_tournois_show", methods={"GET"})
     */
    public function show(Tournois $tournoi): Response
    {
        return $this->render('tournois/show.html.twig', [
            'tournoi' => $tournoi,
        ]);
    }

    /**
     * @Route("/{idtournois}/edit", name="app_tournois_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Tournois $tournoi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TournoisType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tournois_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournois/edit.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idtournois}", name="app_tournois_delete", methods={"POST"})
     */
    public function delete(Request $request, Tournois $tournoi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tournoi->getIdtournois(), $request->request->get('_token'))) {
            $entityManager->remove($tournoi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tournois_index', [], Response::HTTP_SEE_OTHER);
    }




    

  /**
     * @Route("/delete/{idtournois}", name="app_participation_delete", methods={"POST"})
     */
    public function deleteparticipation(Request $request, Tournois $tournoi, EntityManagerInterface $entityManager,$idtournois): Response
    {
        $user1 = $this->get('security.token_storage')->getToken()->getUser();

        $Equipe = $this->getDoctrine()->getRepository(Equipe::class)->find($user1->getIdEquipe());
        $Tournois = $this->getDoctrine()->getRepository(Tournois::class)->find($idtournois);
            $Tournois->removeIdequipe($Equipe);
            $entityManager->flush();
    
        return $this->redirectToRoute('app_participation_indexq', [], Response::HTTP_SEE_OTHER);
    }









       /**
     * @Route("/new/statistique", name="Tournois_stats")
     */

    public function statistiques(Request $request , TournoisRepository $TournoisRep){
        $Tournois= [];
        $Tournois= $this->getDoctrine()->getRepository(Tournois::class)->findAll();
        $Jeux= $this->getDoctrine()->getRepository(Jeux::class)->findAll();

        $categNom=[];
        $Tournoiscount=[];   
  

        $checker=[];
      
        foreach($Tournois as $tournois){

            if(in_array( $tournois->getidjeux() , $checker) ){
               
            }else{
                $checker[]=$tournois->getidjeux();
            $Tournoisx=$TournoisRep->countbyjeux($tournois->getidjeux());

          $categNom[]= $tournois->getidjeux()->getNomjeux(); 
           $Tournoiscount[]= $Tournoisx[0]['count'];
                
            }

        }

      
  
        return $this->render('tournois/tournoisStats.html.twig',[
          'categNom'=>json_encode($categNom),
          'rolescount'=>json_encode($Tournoiscount),
  
        ]);
    }
  

    /**
     * @Route("/s/searchTour", name="searchTour")
     */
    public function searchTournois(Request $request,NormalizerInterface $Normalizer,TournoisRepository $repository,SerializerInterface $serializer):Response
    {
        $requestString=$request->get('searchValue');
        $Tournois = $repository->findByNom($requestString);
        $jsonContent = $serializer->serialize($Tournois, 'json',['Groups'=>'Tournois']);
        $retour =json_encode($jsonContent);
        return new Response($retour);

    }


        /**
         * @Route("event/calendar", name="calendar")
         */
        public function calendar(): Response
        {
            // $event = $calendar->findAll();
            $event = $this->getDoctrine()->getRepository(Tournois::class)->findAll();
            $rdvs = [];
            $allDay = true;
            foreach ($event as $event) {
                $rdvs[] = [
                    'id' => $event->getIdtournois(),
                    'start' => $event->getDateDebut()->format('Y-m-d H:i:s'),
                    'end' => $event->getDateFin()->format('Y-m-d H:i:s'),
                    'title' => $event->getTitre(),
                    'description' => $event->getDescriptiontournois(),
                    'backgroundColor' => "#45bf98",
                    'borderColor' => "#000000",
                    'textColor' => "#ffffff",
                    'allDay' => $allDay,

                ];
            }
            $data = json_encode($rdvs);
            return $this->render('tournois/test.html.twig', compact('data'));
        }


     /**
     * @Route("/s/AllTournois", name="AllTournois")
     */
    public function AllTournois(NormalizerInterface $Normalizer )
    {
    //Nous utilisons la Repository pour récupérer les objets que nous avons dans la base de données
    $repository =$this->getDoctrine()->getRepository(Tournois::class);
    $Tournois=$repository->findAll();
    //Nous utilisons la fonction normalize qui transforme en format JSON nos donnée qui sont
    //en tableau d'objet Students
    $jsonContent=$Normalizer->normalize($Tournois,'json',['groups'=>'post:read']);
    return new Response(json_encode($jsonContent));
    dump($jsonContent);
    die;
}


    /**
     * @Route("/s/deleteTournois/{idtournois}", name="app_TournoisM_delete")
     */
    public function removeTournois(EntityManagerInterface $entityManager,Request $request): Response
    {

        $id = $request->get("idtournois");
        $Tournois= $entityManager
            ->getRepository(Tournois::class)
            ->findOneBy(array('idtournois' => $id));
        if($Tournois!=null ) {
            $entityManager->remove($Tournois);
            $entityManager->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Tournois a ete supprime avec success.");
            return new JsonResponse($formatted);
        }
        return new JsonResponse("id Tournois invalide");

    }



    /**
     * @Route("/s/AjouterTournoisMobile", name="AjouterTournoisMobile")
     */
    public function AjouterTournoisMobile(Request $request)
    {
        $Tournois = new Tournois();
        $Tournois->setTitre($request->get("titre"));


        $Tournois->setDateDebut(\DateTime::createFromFormat('Y-m-d', "2022-05-11"));
        $Tournois->setDateFin(\DateTime::createFromFormat('Y-m-d', "2022-05-15"));
        $Tournois->setDescriptiontournois($request->get("descriptiontournois"));
        $Tournois->setType($request->get("type"));
        $Tournois->setNbrparticipants($request->get("nbrparticipants"));
        $Tournois->setWinner($request->get("winner"));
        $Tournois->setStatus($request->get("status"));
       // $Tournois->setIdjeux($request->get("idjeux"));

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($Tournois);
            $em->flush();

            return new JsonResponse("Tournois Ajoute!", 200);
        }
        catch (\Exception $ex)
        {
            return new Response("Execption: ".$ex->getMessage());
        }

        //http://127.0.0.1:8000/AjouterCategorieMobile?user=9&produit=6&quantite=5&adresse=bouzid
        //http://127.0.0.1:8000/tournois/s/AjouterTournoisMobile?titre=tz&dateDebut=2017-02-01T00:00:00+01:00&dateFin=2017-03-01T00:00:00+01:00&descriptiontournois=test&type=Equipe&nbrparticipants=11&winner=NULL&status=NULL&idjeux=0

    }



    /**
     * @Route("/s/ModifierTournoisMobile/{idTournois}", name="ModifierTournoisMobile")
     */
    public function ModifierTournoisMobile(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $this->getDoctrine()->getManager()
            ->getRepository(Tournois::class)
            ->find($request->get("idTournois"));


            $commande->setTitre($request->get("titre"));
            $commande->setDateDebut(\DateTime::createFromFormat('Y-m-d', "2022-05-13"));
            $commande->setDateFin(\DateTime::createFromFormat('Y-m-d', "2022-05-16"));
            $commande->setDescriptiontournois($request->get("descriptiontournois"));
            $commande->setType($request->get("type"));
            $commande->setNbrparticipants($request->get("nbrparticipants"));
            $commande->setWinner($request->get("winner"));
            $commande->setStatus($request->get("status"));
            //$commande->setIdjeux($request->get("idjeux"));

        try {
            $em->persist($commande);
            $em->flush();

            return new JsonResponse("Tournois Modifie!", 200);
        }
        catch (\Exception $ex)
        {
            return new Response("Execption: ".$ex->getMessage());
        }

        //http://127.0.0.1:8000/ModifierPodcastsMobile?id=8&user=9&produit=6&quantite=10&adresse=ariana

    }
}

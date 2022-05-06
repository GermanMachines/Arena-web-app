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

use App\Repository\UserRepository;

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




}

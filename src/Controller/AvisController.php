<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Jeux;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\User;

/**
 * @Route("/avis")
 */
class AvisController extends AbstractController
{
    /**
     * @Route("/updateAvisJSON/{id}",name="update_Avis_json" , methods={"GET"})
     */
    public function updateAvisByIdJSON(Request $request, $id, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $avis = new Avis();
        $avis = $em->getRepository(Avis::class)->find($id);
        $avis->setScore($request->get('score'));
        $avis->setCommentaire($request->get('commentaire'));
        $em->flush();

        $json_content = $normalizer->normalize($avis, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($json_content));
    }

    /**
     * @Route("/addAvisJSON", name="add_avis_json", methods={"GET"})
     */
    public function addAvisJson(Request $request, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();

        $idUser = $request->get('iduser');
        $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);

        $idJeux = $request->get('idjeux');
        $jeux = $this->getDoctrine()->getRepository(Jeux::class)->find($idJeux);

        $avis = new Avis();
        $avis->setScore($request->get('score'));
        $avis->setCommentaire($request->get('commentaire'));
        $avis->setIdutulisateur($user); //must pass User
        $avis->setIdjeux($jeux);


        $em->persist($avis);
        $em->flush();
        $json_content = $normalizer->normalize($avis, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($json_content));
    }
    /**
     * @Route("/getAvisJSON/{id}",name="get__avis_json" , methods={"GET"})
     */
    public function getAvisJSON(Request $request, $id, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $avis = $this->getDoctrine()->getRepository(Avis::class)->find($id);
        $json_content = $normalizer->normalize($avis, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($json_content));
    }
    /**
     * @Route("/getAllAvisJSON",name="get_all_avis_json" , methods={"GET"})
     */
    public function getAllAvisJSON(Request $request, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $avis = $this->getDoctrine()->getRepository(Avis::class)->findAll();
        $json_content = $normalizer->normalize($avis, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($json_content));
    }

    /**
     * @Route("/deleteAvisJSON/{id}",name="delete_avis_json" , methods={"GET"})
     */
    public function deleteReclamationByIdJSON(Request $request, $id, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();

        $avis = $this->getDoctrine()->getRepository(Avis::class)->find($id);
        $em->remove($avis);
        $em->flush();
        $json_content = $normalizer->normalize($avis, 'json', ['groups' => 'post:read']);
        return new Response("avis Deleted successfuly" . json_encode($json_content));
    }

    /**
     * @Route("/", name="app_avis_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $avis = $entityManager
            ->getRepository(Avis::class)
            ->findAll();

        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    /**
     * @Route("/front", name="app_avis_index_front", methods={"GET"})
     */
    public function indexFront(EntityManagerInterface $entityManager): Response
    {
        $user1 = $this->get('security.token_storage')->getToken()->getUser();
        $avis = $entityManager
            ->getRepository(Avis::class)
            ->findBy(array('idutulisateur' =>
            $user1->getId()));

        return $this->render('avis/index-front.html.twig', [
            'avis' => $avis,
        ]);
    }

    /**
     * @Route("/new", name="app_avis_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($avi);
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("front/new", name="app_avis_new_front", methods={"GET", "POST"})
     */
    public function newFront(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jeuxId = $request->get('id');
        $game = $this->getDoctrine()->getRepository(Jeux::class)->find($jeuxId);
        // dd($game);
        $avi = new Avis();
        $avi->setIdjeux($game);

        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);
        $user1 = $this->get('security.token_storage')->getToken()->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            $avi->setIdutulisateur($user1);
            $entityManager->persist($avi);
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_index_front', [], Response::HTTP_SEE_OTHER);
        }
        # $avi->setIdjeux($id);
        # $form=$this->CreateForm(Avis::Class,$avi);

        return $this->render('avis/new-front.html.twig', [
            'avi' => $avi,
            'form' => $form->createView(),
            'jeu' => $game
        ]);
    }




    /**
     * @Route("/{id}", name="app_avis_show", methods={"GET"})
     */
    public function show(Avis $avi): Response
    {
        return $this->render('avis/show.html.twig', [
            'avi' => $avi,
        ]);
    }


    /**
     * @Route("front/{id}", name="app_avis_show_front", methods={"GET"})
     */
    public function showFront(Avis $avi): Response
    {
        return $this->render('avis/show-front.html.twig', [
            'avi' => $avi,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_avis_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("front/{id}/edit", name="app_avis_edit_front", methods={"GET", "POST"})
     */
    public function editFront(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);
        // dd($avi->getIdjeux()->getNomjeux());
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/edit-front.html.twig', [
            'avi' => $avi,
            'form' => $form->createView(),
            'jeu' => $avi->getIdjeux()
        ]);
    }

    /**
     * @Route("/{id}", name="app_avis_delete", methods={"POST"})
     */
    public function delete(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $avi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($avi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("front/{id}", name="app_avis_delete_front", methods={"POST"})
     */
    public function deleteFront(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $avi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($avi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_avis_index_front', [], Response::HTTP_SEE_OTHER);
    }
}

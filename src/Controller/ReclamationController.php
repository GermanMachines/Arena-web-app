<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\User;
use App\Form\EmailType;
use App\Form\EreclamationType;
use App\Form\ReclamationFrontType;
use App\Form\ReclamationType;
use App\Form\SearchReclamationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;



/**
 * @Route("/reclamation")
 */
class ReclamationController extends AbstractController
{
    /**
     * @Route("/search", name="app_reclamation_search")
     */
    public function searchAdavanced(Request $request): Response
    {


        $input = $request->get('search_reclamation')['search'];
        $form = $this->createForm(SearchReclamationType::class);
        $reclamations = $this->getDoctrine()->getRepository(Reclamation::class)->search($input);

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/", name="app_reclamation_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(SearchReclamationType::class);
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/front", name="app_reclamation_index_front", methods={"GET"})
     */
    public function indexFront(EntityManagerInterface $entityManager): Response
    {
        $user1 = $this->get('security.token_storage')->getToken()->getUser();

        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findBy(array('iduser' =>
            $user1->getId()));

        return $this->render('reclamation/index-front.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    /**
     * @Route("/new", name="app_reclamation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime('@' . strtotime('now'));
            $reclamation->setDate($date);
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Email sent successfully');
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("front/new", name="app_reclamation_new_front", methods={"GET", "POST"})
     */
    public function newFront(UserRepository $repository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationFrontType::class, $reclamation);
        $form->handleRequest($request);

        $session = $request->getSession();

        $users = $repository->findAll();
        $user1 = $this->get('security.token_storage')->getToken()->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime('@' . strtotime('now'));
            $reclamation->setDate($date);
            $reclamation->setEtat(0);
            $reclamation->setIduser($user1);
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new-front.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}", name="app_reclamation_show", methods={"GET"})
     */
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    /**
     * @Route("front/{id}", name="app_reclamation_show_front", methods={"GET"})
     */
    public function showFront(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show-front.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }


    /**
     * @Route("front/{id}/edit", name="app_reclamation_edit_front", methods={"GET", "POST"})
     */
    public function editFront(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationFrontType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit-front.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_reclamation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}", name="app_reclamation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("front/delete/{id}", name="app_reclamation_delete_front", methods={"POST"})
     */
    public function deleteFront(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index_front', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/reclamation/download", name="app_reclamation_download")
     */
    public function downloadPdf(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('reclamation/download.html.twig', [
            'reclamations' => $reclamations
        ]);


        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("reclamation-data.pdf", [
            "Attachment" => true
        ]);
        $this->addFlash('success', 'download started !');
        //  return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}

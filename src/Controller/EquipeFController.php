<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Equipe;
use App\Form\EquipeType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;


class EquipeFController extends AbstractController
{
    /**
     * @Route("/equipe/f", name="app_equipe_f")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $equipes = $entityManager
            ->getRepository(Equipe::class)
            ->findAll();

        return $this->render('equipe_f/index.html.twig', [
            'equipes1' => $equipes,
        ]);
    }
     /**
     * @Route("/{idequipe}", name="app_equipe_show1", methods={"GET"})
     */
    public function show1(Equipe $equipe): Response
    {
        return $this->render('equipe_f/show1.html.twig', [
            'equipe' => $equipe,
        ]);
    }

}

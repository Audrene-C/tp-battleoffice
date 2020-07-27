<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Form\AdressesType;
use App\Repository\AdressesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/adresses")
 */
class AdressesController extends AbstractController
{
    /**
     * @Route("/", name="adresses_index", methods={"GET"})
     */
    public function index(AdressesRepository $adressesRepository): Response
    {
        return $this->render('adresses/index.html.twig', [
            'adresses' => $adressesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="adresses_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $adress = new Adresses();
        $form = $this->createForm(AdressesType::class, $adress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adress);
            $entityManager->flush();

            return $this->redirectToRoute('adresses_index');
        }

        return $this->render('adresses/new.html.twig', [
            'adress' => $adress,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="adresses_show", methods={"GET"})
     */
    public function show(Adresses $adress): Response
    {
        return $this->render('adresses/show.html.twig', [
            'adress' => $adress,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="adresses_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Adresses $adress): Response
    {
        $form = $this->createForm(AdressesType::class, $adress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('adresses_index');
        }

        return $this->render('adresses/edit.html.twig', [
            'adress' => $adress,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="adresses_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Adresses $adress): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adress->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($adress);
            $entityManager->flush();
        }

        return $this->redirectToRoute('adresses_index');
    }
}

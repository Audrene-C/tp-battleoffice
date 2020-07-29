<?php

namespace App\Controller;

use App\Entity\Addresses;
use App\Form\AddressesType;
use App\Repository\AddressesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Addresses")
 */
class AddressesController extends AbstractController
{
    /**
     * @Route("/", name="Addresses_index", methods={"GET"})
     */
    public function index(AddressesRepository $AddressesRepository): Response
    {
        return $this->render('Addresses/index.html.twig', [
            'Addresses' => $AddressesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="Addresses_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $address = new Addresses();
        $form = $this->createForm(AddressesType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('Addresses_index');
        }

        return $this->render('Addresses/new.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="Addresses_show", methods={"GET"})
     */
    public function show(Addresses $address): Response
    {
        return $this->render('Addresses/show.html.twig', [
            'address' => $address,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="Addresses_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Addresses $address): Response
    {
        $form = $this->createForm(AddressesType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('Addresses_index');
        }

        return $this->render('Addresses/edit.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="Addresses_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Addresses $address): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($address);
            $entityManager->flush();
        }

        return $this->redirectToRoute('Addresses_index');
    }
}

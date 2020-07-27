<?php

namespace App\Controller;

use App\Entity\Billing;
use App\Form\BillingType;
use App\Repository\BillingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/billing")
 */
class BillingController extends AbstractController
{
    /**
     * @Route("/", name="billing_index", methods={"GET"})
     */
    public function index(BillingRepository $billingRepository): Response
    {
        return $this->render('billing/index.html.twig', [
            'billings' => $billingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="billing_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $billing = new Billing();
        $form = $this->createForm(BillingType::class, $billing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($billing);
            $entityManager->flush();

            return $this->redirectToRoute('billing_index');
        }

        return $this->render('billing/new.html.twig', [
            'billing' => $billing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="billing_show", methods={"GET"})
     */
    public function show(Billing $billing): Response
    {
        return $this->render('billing/show.html.twig', [
            'billing' => $billing,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="billing_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Billing $billing): Response
    {
        $form = $this->createForm(BillingType::class, $billing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('billing_index');
        }

        return $this->render('billing/edit.html.twig', [
            'billing' => $billing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="billing_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Billing $billing): Response
    {
        if ($this->isCsrfTokenValid('delete'.$billing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($billing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('billing_index');
    }
}

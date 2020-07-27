<?php

namespace App\Controller;

use App\Entity\Billing;
use App\Entity\Client;
use App\Form\BillingType;
use App\Entity\Shipping;
use App\Form\ClientType;
use App\Form\ShippingType;
use App\Manager\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingPageController extends AbstractController
{
    /**
     * @Route("/", name="landing_page")
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $formArray = [
            'client' => new Client,
            'billing' => new Billing,
            'shipping' => new Shipping
        ];

        $form = $this->createFormBuilder($formArray)
        ->add('client', ClientType::class)
        ->add('billing', BillingType::class)
        ->add('shipping', ShippingType::class)
        ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            dd($request);
            $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist();
            // $entityManager->flush();

            return $this->redirectToRoute('order_index');
        }

        return $this->render('landing_page/index_new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/confirmation", name="confirmation")
     */
    public function confirmation()
    {
        return $this->render('landing_page/confirmation.html.twig', [

        ]);
    }
}

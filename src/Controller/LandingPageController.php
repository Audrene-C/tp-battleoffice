<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Billing;
use App\Entity\Client;
use App\Entity\Order;
use App\Form\BillingType;
use App\Entity\Shipping;
use App\Form\ClientType;
use App\Form\ShippingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LandingPageController extends AbstractController
{
    public function createClient(Request $request)
    {
        $dataForm = $request->request->get('form');
        $client = new Client;
        $client->setFirstname($dataForm['client']['firstname']);
        $client->setLastname($dataForm['client']['lastname']);
        $client->setEmail($dataForm['client']['email']['first']);

        return $client;
    }

    public function createBilling(Request $request)
    {
        $dataForm = $request->request->get('form');
        $billing = new Billing;
        $billing->setAdressLine1(($dataForm['billing']['adressLine1']));
        $billing->setAdressLine2(($dataForm['billing']['adressLine2']));
        $billing->setCity(($dataForm['billing']['city']));
        $billing->setZipcode(($dataForm['billing']['zipcode']));
        $billing->setCountry(($dataForm['billing']['country']));
        $billing->setPhone(($dataForm['billing']['phone']));

        return $billing;
    }

    public function createShipping(Request $request, Client $client, Billing $billing)
    {
        $dataForm = $request->request->get('form');
        $shippingTable = $dataForm['shipping'];
        
        $countEmpty = 0;
        $shipping = new Shipping;
        foreach ($shippingTable as $key) {
            if (empty($key)) {
                $countEmpty += 1;
            }
        }
        
        if ($countEmpty === 8) {
            $shipping->setClientFirstName($client->getFirstname());
            $shipping->setClientLastName($client->getLastname());
            $shipping->setAdressLine1($billing->getAdressLine1());
            $shipping->setAdressLine2($billing->getAdressLine2());
            $shipping->setCity($billing->getCity());
            $shipping->setZipcode($billing->getZipcode());
            $shipping->setCountry($billing->getCountry());
            $shipping->setPhone($billing->getPhone());
            
            return $shipping;

        } else {
            $shipping->setClientFirstName($shippingTable['clientFirstName']);
            $shipping->setClientLastName($shippingTable['clientLastName']);
            $shipping->setAdressLine1($shippingTable['adressLine1']);
            $shipping->setAdressLine2($shippingTable['adressLine2']);
            $shipping->setCity($shippingTable['city']);
            $shipping->setZipcode($shippingTable['zipcode']);
            $shipping->setCountry($shippingTable['country']);
            $shipping->setPhone($shippingTable['phone']);

            return $shipping;
        }
    }

    public function createOrder(Request $request, Client $client)
    {
        $orderTable = $request->request->get('order');
        $product = $orderTable['cart']['cart_products'][0];
        $paymentMethod = $orderTable['payment_method'];

        $order = new Order;
        $order->setClient($client);
        $order->setProduct($product);
        $order->setPaymentMethod($paymentMethod);
        $order->setStatus('WAITING');

        return $order;
    }

    public function createAdresses(Billing $billing, Shipping $shipping)
    {
        $adresses = new Adresses;
        $adresses->setBilling($billing);
        $adresses->setShipping($shipping);

        return $adresses;
    }

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
        ->setAction($this->generateUrl('https://api-commerce.simplon-roanne.com/order'))
        ->setMethod('POST')
        ->add('client', ClientType::class)
        ->add('billing', BillingType::class)
        ->add('shipping', ShippingType::class)
        ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $client = $this->createClient($request);
            $billing = $this->createBilling($request);
            $shipping = $this->createShipping($request, $client, $billing);
            $order = $this->createOrder($request, $client);
            $adresses = $this->createAdresses($billing, $shipping);            
            
            //dd($client, $billing, $shipping, $order);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->persist($billing);
            $entityManager->persist($shipping);
            $entityManager->persist($order);
            $entityManager->persist($adresses);
            $entityManager->flush();

            return $this->redirectToRoute('order_index', [
                'order' => $order
            ]);
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

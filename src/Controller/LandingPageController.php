<?php

namespace App\Controller;

use App\Entity\Addresses;
use App\Entity\Billing;
use App\Entity\Client;
use App\Entity\Order;
use App\Form\BillingType;
use App\Entity\Shipping;
use App\Form\ClientType;
use App\Form\ShippingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpClient\HttpClient;

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

    public function createAddresses(Billing $billing, Shipping $shipping)
    {
        $addresses = new Addresses;
        $addresses->setBilling($billing);
        $addresses->setShipping($shipping);

        return $addresses;
    }

    public function sendRequest(Order $order, Addresses $addresses)
    {
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);
        
        $jsonOrder = $serializer->serialize($order, 'json');
        $jsonAddresses = $serializer->serialize($addresses, 'json');
        $table = array('order' => $jsonOrder, 'addresses' => $jsonAddresses);
        $json = $encoder->encode($table, 'json');
        echo($json);
        dd($json);

        // dd($jsonOrder, $jsonAddresses);
        
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'https://api-commerce.simplon-roanne.com/order', [
            'headers' => [
                'accept'=> 'application/json',
                'Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX',
                'Content-Type' => 'application/json',
            ],
            'body' => '{
                "order": {
                  "id": 1,
                  "product": "Nerf Elite Jolt",
                  "payment_method": "paypal",
                  "status": "WAITING",
                  "client": {
                    "firstname": "Audrene",
                    "lastname": "Et Greg un peu",
                    "email": "francois.dupont@gmail.com"
                  },
                  "addresses": {
                    "billing": {
                      "address_line1": "1, rue du test",
                      "address_line2": "3ème étage",
                      "city": "Lyon",
                      "zipcode": "69000",
                      "country": "France",
                      "phone": "string"
                    },
                    "shipping": {
                      "address_line1": "1, rue du test",
                      "address_line2": "3ème étage",
                      "city": "Lyon",
                      "zipcode": "69000",
                      "country": "France",
                      "phone": "string"
                    }
                  }
                }
              }'
            // 'json' => [
            //     'order' => $order,
            //     'addresses' => $addresses,
            // ],
        ]);
        return $response;
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
            $addresses = $this->createAddresses($billing, $shipping);

            $table = array('order' => $order, 'addresses' =>$addresses);

            $response = $this->sendRequest($order, $addresses);
            $statusCode = $response->getStatusCode(); 
            dd($statusCode);        
            
            //dd($client, $billing, $shipping, $order);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->persist($billing);
            $entityManager->persist($shipping);
            $entityManager->persist($order);
            $entityManager->persist($addresses);
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

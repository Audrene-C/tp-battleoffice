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
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

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
        $billing->setAddressLine1(($dataForm['billing']['addressLine1']));
        $billing->setAddressLine2(($dataForm['billing']['addressLine2']));
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
            if (!is_null($key)) {
                $countEmpty += 1;
            }
        }
        
        if ($countEmpty === 8) {
            $shipping->setClientFirstName($client->getFirstname());
            $shipping->setClientLastName($client->getLastname());
            $shipping->setAddressLine1($billing->getAddressLine1());
            $shipping->setAddressLine2($billing->getAddressLine2());
            $shipping->setCity($billing->getCity());
            $shipping->setZipcode($billing->getZipcode());
            $shipping->setCountry($billing->getCountry());
            $shipping->setPhone($billing->getPhone());
            
            return $shipping;

        } else {
            $shipping->setClientFirstName($shippingTable['clientFirstName']);
            $shipping->setClientLastName($shippingTable['clientLastName']);
            $shipping->setAddressLine1($shippingTable['addressLine1']);
            $shipping->setAddressLine2($shippingTable['addressLine2']);
            $shipping->setCity($shippingTable['city']);
            $shipping->setZipcode($shippingTable['zipcode']);
            $shipping->setCountry($shippingTable['country']);
            $shipping->setPhone($shippingTable['phone']);

            return $shipping;
        }
    }

    public function createAddresses(Billing $billing, Shipping $shipping)
    {
        $addresses = new Addresses;
        $addresses->setBilling($billing);
        $addresses->setShipping($shipping);

        return $addresses;
    }

    public function createOrder(Request $request, Client $client, Addresses $addresses)
    {
        $orderTable = $request->request->get('order');
        $product = $orderTable['cart']['cart_products'][0];
        $paymentMethod = $orderTable['payment_method'];

        $order = new Order;
        $order->setClient($client);
        $order->setProduct($product);
        $order->setPaymentMethod($paymentMethod);
        $order->setStatus('WAITING');
        $order->setAddresses($addresses);

        return $order;
    }

    public function sendRequest(Order $order)
    {
        // $encoder = new JsonEncoder();
        // $normalizer = new ObjectNormalizer();
        // $serializer = new Serializer([$normalizer], [$encoder]);

        // $orderTable = $normalizer->normalize($order);
        
        // $table = array('order' => $orderTable);
        // $json = $encoder->encode($table, 'json');
        // dd($json);
        
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'https://api-commerce.simplon-roanne.com/order', [
            'headers' => [
                'accept'=> 'application/json',
                'Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Audrene was here',
            ],
            'json' => [
                'order' => [
                    'id' => $order->getId(),
                    'product' => $order->getProduct(),
                    'payment_method' => $order->getPaymentMethod(),
                    'status' => $order->getStatus(),
                    'client' => [
                        'firstname' => $order->getClient()->getFirstname(),
                        'lastname' => $order->getClient()->getLastname(),
                        'email' => $order->getClient()->getEmail(),
                    ],
                    'addresses' => [
                        'billing' => [
                            'address_line1' => $order->getAddresses()->getBilling()->getAddressLine1(),
                            'address_line2' => $order->getAddresses()->getBilling()->getAddressLine2(),
                            'city' => $order->getAddresses()->getBilling()->getCity(),
                            'zipcode' => $order->getAddresses()->getBilling()->getZipcode(),
                            'country' => $order->getAddresses()->getBilling()->getCountry(),
                            'phone' => $order->getAddresses()->getBilling()->getPhone(),
                        ],
                        'shipping' => [
                            'address_line1' => $order->getAddresses()->getShipping()->getAddressLine1(),
                            'address_line2' => $order->getAddresses()->getShipping()->getAddressLine2(),
                            'city' => $order->getAddresses()->getShipping()->getCity(),
                            'zipcode' => $order->getAddresses()->getShipping()->getZipcode(),
                            'country' => $order->getAddresses()->getShipping()->getCountry(),
                            'phone' => $order->getAddresses()->getShipping()->getPhone(),
                        ]
                    ]
                ]
            ]
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
            $addresses = $this->createAddresses($billing, $shipping);
            $order = $this->createOrder($request, $client, $addresses);

            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->persist($billing);
            $entityManager->persist($shipping);
            $entityManager->persist($addresses);
            $entityManager->persist($order);
            $entityManager->flush();
            
            $response = $this->sendRequest($order);

            if($response->getStatusCode() === 200) {

                $encoder = new JsonEncoder();
                $content = $encoder->decode($response->getContent(), 'array');
                $apiId = $content['order_id'];
                
                $order->setApiId($apiId);
                $entityManager->persist($order);
                $entityManager->flush();
                        
                return $this->redirectToRoute('payment', [
                    'id' => $order->getId()
                ]);
            }
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

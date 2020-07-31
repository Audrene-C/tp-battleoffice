<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Payment;
use App\Form\PaymentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;

class PaymentController extends AbstractController
{
    public function updateStatus(Order $order)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'https://api-commerce.simplon-roanne.com/order/'.$order->getApiId().'/status', [
            'headers' => [
                'accept' => 'application/json',
                'Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Audrene was here',
            ],
            'json' => [
                'status' => 'PAID'
            ]
        ]);
        return $response;
    }

    /**
     * @Route("/{id}/payment", name="payment")
     */
    public function index(Request $request, Order $order)
    {
        $payment = new Payment;

        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
            // Set your secret key. Remember to switch to your live secret key in production!
            // See your keys here: https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey('sk_test_51HABwtFXlDry4DEkHdXIou7Z8smjzsVBOVTuw7xgi0M6e8QomPiWK6bBWMSvH6y3euvzmc3w9VRfzMAOv9o9T5wm00BjMXHkcM');

            // Token is created using Stripe Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $token = $_POST['stripeToken'];
            $charge = \Stripe\Charge::create([
            'amount' => 999,
            'currency' => 'usd',
            'description' => 'Example charge',
            'source' => 'tok_visa',
            ]);
            } catch(\Stripe\Exception\CardException $e) {

                return $this->redirectToRoute('payment', [
                    'id' => $order->getId()
                ]);
            }

            $response = $this->updateStatus($order);
            //dd($response->getContent(), $response->getStatusCode());

            if($response->getStatusCode() === 200) {

                $order->setStatus('PAID');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($order);
                $entityManager->flush();
                return $this->redirectToRoute('confirmation', [
                    'id' => $order->getId()
                ]);
            }
        }

        return $this->render('payment/payment.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

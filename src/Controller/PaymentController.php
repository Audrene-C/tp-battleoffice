<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Payment;
use App\Form\PaymentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    /**
     * @Route("/{id}/payment", name="payment")
     */
    public function index(Request $request, Order $order)
    {
        $payment = new Payment;

        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            dd($request->request);

            return $this->redirectToRoute('landing_page');
        }

        return $this->render('payment/payment.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

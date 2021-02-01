<?php

namespace App\Controller;


use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\Meal;
use Stripe\Customer;
use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\MealRepository;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    

     /**
     * Affiche une commande
     * @Route("/order/{id}",name="order_show")
     * 
     * @param Order $order
     * @return Response
     */
    public function show(Order $order, Request $request,ObjectManager $manager){

      

        return $this->render("order/show.html.twig",['order'=>$order]);
    }

     /**
     * Permet d'afficher le formulaire de commande de menu
     * @Route("/meal/{slug}/order", name="order_create")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function order(Meal $meal, Request $request,ObjectManager $manager)

    {
        $order = new Order();

        $form = $this->createForm(OrderType::class,$order);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $user = $this->getUser();
            $amount = $meal->getPrice();
            $payment=0;
            
                                         
            $order->setCustomer($user)
                  ->addMeal($meal)
                  ->setAmount($amount)
                  ->setPayment($payment)
                ;
            
            $manager->persist($order);
            $manager->flush();

            return $this->redirectToRoute("order_payment",['id'=>$order->getId(),'alert'=>true]);


        }

        return $this->render('order/order.html.twig', [
           
            'form'=>$form->createView(),
            'meal'=>$meal
            
        ]);
       
    }

     /**
     * Permet d'afficher le formulaire de commande de menu
     * @Route("/meal/order/payment/{id}", name="order_payment")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function payment(Order $order, OrderRepository $repoOrder,Request $request,ObjectManager $manager)

    {

        \Stripe\Stripe::setApiKey('sk_test_51GuiHFFbJaAsUUQtK1IoRCLT7169fyhN4U2WvuC0P8sXGc7pT1bKK5AxDHg9COT1k7BmgvBsG2ComLNdDoKa8Fwr00MuPB1kC7');
        
        if(isset($_POST['stripeToken'])){
            $customer = \Stripe\Customer::create([
                'description' => 'Nom et Prénom',
                'source' => $_POST['stripeToken'],
                'email' => 'email@test.fr'
                ]);
        $charge = \Stripe\Charge::create([
            'amount' => $_POST['total']*100,
            'currency' => 'eur',
            'customer' => $customer->id
        ]);

    

        //var_dump($charge->status);
        if($charge->status == 'succeeded'){
            // paiement validé

            
                 // on relie l'image à l'annonce et on modifie l'annonce
                $payment = 1;
                $order->setPayment($payment);

                // on sauvegarde les images
                $manager->persist($order);
                $manager->flush();
            
            return $this->render('order/paymentok.html.twig');
        }else{
            return $this->render('order/payment.html.twig');
            $this->addFlash("warning","Le paiement a échoué !");
        }
        }
    return $this->render('order/payment.html.twig',[

      
        'order'=>$order
        
    ]);
        
       
    }



     

       

}

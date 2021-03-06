<?php

namespace App\Controller;


use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\Meal;
use Stripe\Customer;
use App\Entity\Order;
use App\Entity\Comment;
use App\Form\OrderType;
use App\Form\CommentType;
use Symfony\Component\Mime\Email;
use App\Repository\MealRepository;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Config\Definition\Exception\Exception;
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


        $comment = new Comment();

        $form = $this->createForm(CommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $comment->setMeal($order->getMeal())
                    ->setAuthor($this->getUser())
                    ;

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash("success","Votre commentaire a bien été enregistré.");
        }
        return $this->render("order/show.html.twig",[
            'order'=>$order,
            'form'=>$form->createView()
            ]);
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
            $amountqty= $meal->getPrice() * $form->get('qty')->getData();
            $amount =floatval($amountqty);
            $payment=0;
        
            
                                         
            $order->setCustomer($user)
                  ->setMeal($meal)
                  ->setAmount($amount)
                  ->setPayment($payment)
                ;
            
            $manager->persist($order);
            $manager->flush();

            return $this->redirectToRoute("order_payment",['id'=>$order->getId(), 'alert'=>true]);


        }

        return $this->render('order/order.html.twig', [
           
            'form'=>$form->createView(),
            'meal'=>$meal
            
        ]);
       
    }

     /**
     * Permet d'afficher d'afficher le formulaire de paiement
     * @Route("/meal/order/payment/{id}", name="order_payment")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function payment(MailerInterface $mailer,Order $order, OrderRepository $repoOrder,Request $request,ObjectManager $manager)

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

        if($charge->status == 'succeeded'){
            // paiement validé

      /*       // envoi du mail
                $html = "</style>
                <!-- move the above styles into your custom stylesheet -->
                <spacer size='16'></spacer>
                <container>
                <spacer size='16'></spacer>
                <row>
                    <columns>
                    <h1>Merci pour votre commande</h1>
                    <p></p>
                    <spacer size='16'></spacer>
                    <callout class='secondary'>
                        <row>
                        <columns large='6'>
                            <p>
                            <strong>Type de paiement</strong><br/>
                            Carte Bancaire
                            </p>
                            <p>
                            <strong>Adresse mail</strong><br/>";
             
                $html.="  </p><p><strong>Numero de commande</strong><br/>";
               
                $html.=" </p>
                    </columns>
                    <columns large='6'>
                    <p>
                        <strong>Type de livraison</strong><br/>
                        Colissimo (2 jours)<br/><br>
                        <strong>Adresse de livraison</strong><br/>
                        Captain Price<br/>
                        123 Maple Rd<br/>
                        Campbell, CA 95112
                    </p>
                    </columns>
                </row>
                </callout>
                </columns></row></container>";

                try{
                
                $email = (new Email())
                ->from('contact@shangrila.fr')
                ->to('m.limeul@gmail.com')
                ->subject('Confirmation de votre commande')
                ->html($html);
       
                $email->SMTPOptions = array(
                    'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                    )
                    );

               

                $mailer->send($email);


                } catch (Exception $e) {
                                
                    $this->addFlash("Message could not be sent. Mailer Error: {$email->ErrorInfo}");
                }
                 */
            
            // on relie l'image à l'annonce et on modifie l'annonce
            $payment = 1;
            $order->setPayment($payment);

            // on sauvegarde les images
            $manager->persist($order);
            $manager->flush();
            
            return $this->redirectToRoute("order_paymentok",['id'=>$order->getId(), 'alert'=>true]);

        }else{

            return $this->render('order/payment.html.twig');
            $this->addFlash("warning","Le paiement a échoué !");
        }
        }
    return $this->render('order/payment.html.twig',[

        'order'=>$order
        
    ]);
        
       
    }

     /**
     * Affiche le paiement réussi
     * @Route("/meal/order/paymentOK/{id}",name="order_paymentok")
     * 
     * @param Order $order
     * @return Response
     */
    public function paymentOK(Order $order, Request $request,ObjectManager $manager){

      
        return $this->render("order/paymentok.html.twig",[
            'order'=>$order
            ]);
    }




     

       

}

<?php

namespace App\Controller;

use App\Entity\BookingTable;
use App\Form\BookingTableType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    
     /**
     * Permet d'afficher le formulaire de réservation de table
     * @Route("/", name="homepage")
     * 
     * 
     * 
     * @return Response
     */
    public function reservation(Request $request,ObjectManager $manager)

    {
        $booking = new BookingTable();

        $form = $this->createForm(BookingTableType::class,$booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $user = $this->getUser();
            if($user != null){
            
            $booking->setUsers($user);
            
            $manager->persist($booking);
            $manager->flush();

            return $this->redirectToRoute("bookingtable_show",['id'=>$booking->getId(),'alert'=>true]);
            }else{
                
                return $this->redirectToRoute("account_login"); 
            }
            
        }

        return $this->render('home.html.twig', [
           
            'form'=>$form->createView()
            
        ]);
    }

  
}

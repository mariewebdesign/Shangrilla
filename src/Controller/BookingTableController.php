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

class BookingTableController extends AbstractController
{
     
    /**
     * Affiche une réservation
     * @Route("/reservation/{id}",name="bookingtable_show")
     * 
     * @param BookingTable $booking
     * @return Response
     */
    public function show(BookingTable $booking, Request $request,ObjectManager $manager){

      

        return $this->render("booking_table/show.html.twig",['booking'=>$booking]);
    }
}

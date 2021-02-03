<?php

namespace App\Controller;


use App\Service\Pagination;
use App\Entity\BookingTable;
use App\Form\AdminBookingTableType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingTableController extends AbstractController
{
    /**
     * Affichage de la liste des réservations
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_list")
     * 
     * @return Response
     */
    public function index(Pagination $paginationService,$page)
    {
        $paginationService->setEntityClass(BookingTable::class)
                          ->setPage($page)
                          //->setRoute('admin_bookings_list')
                            ;


        return $this->render('admin/bookingtable/index.html.twig', [
            'pagination' =>$paginationService
        ]);
    }

         /**
     * Suppression d'une réservation
     * @Route("/admin/bookings/{id}/delete",name="admin_bookings_delete")
     *
     * @param BookingTable $booking
     * @return Response
     */
    public function delete(BookingTable $booking,ObjectManager $manager){

        $manager->remove($booking);
        $manager->flush();

        $this->addFlash("success","La réservation n°{$booking->getId()} a bien été supprimé !");


        return $this->redirectToRoute("admin_bookings_list");

    }

    /**
     * Edition des réservations
     * @Route("admin/bookings/{id}/edit",name="admin_bookings_edit")
     * @param bookingtable $booking
     * @param Request $request
     * @return Response
     */
    public function edit(BookingTable $booking, Request $request,ObjectManager $manager){

        $form = $this->createForm(AdminBookingTableType::class,$booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           // $booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash('success',"La réservation n°{$booking->getId()} a bien été modifiée");
            return $this->redirectToRoute('admin_bookings_list');
        }

        return $this->render('admin/bookingtable/edit.html.twig',[
            'booking'=>$booking,
            'form'=>$form->createView()
        ]);
    }
}

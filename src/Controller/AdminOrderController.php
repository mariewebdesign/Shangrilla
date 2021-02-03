<?php

namespace App\Controller;


use App\Entity\Order;

use App\Service\Pagination;
use App\Form\AdminOrderType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminOrderController extends AbstractController
{
    /**
     * Affichage de la liste des commandes
     * @Route("/admin/orders/{page<\d+>?1}", name="admin_orders_list")
     * 
     * @return Response
     */
    public function index(Pagination $paginationService,$page)
    {
        $paginationService->setEntityClass(Order::class)
                          ->setPage($page)
                          //->setRoute('admin_orders_list')
                            ;


        return $this->render('admin/order/index.html.twig', [
            'pagination' =>$paginationService
        ]);
    }

         /**
     * Suppression d'une commande
     * @Route("/admin/orders/{id}/delete",name="admin_orders_delete")
     *
     * @param Order $order
     * @return Response
     */
    public function delete(Order $order,ObjectManager $manager){

        $manager->remove($order);
        $manager->flush();

        $this->addFlash("success","La commande n°{$order->getId()} a bien été supprimée !");


        return $this->redirectToRoute("admin_orders_list");

    }

    /**
     * Edition des commandes
     * @Route("admin/orders/{id}/edit",name="admin_orders_edit")
     * @param Order $order
     * @param Request $request
     * @return Response
     */
    public function edit(Order $order, Request $request,ObjectManager $manager){

        $form = $this->createForm(AdminOrderType::class,$order);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

       
            $order->setAmount(0);

            $manager->persist($order);
            $manager->flush();

            $this->addFlash('success',"La commande n°{$order->getId()} a bien été modifiée");
            return $this->redirectToRoute('admin_orders_list');
        }

        return $this->render('admin/order/edit.html.twig',[
            'order'=>$order,
            'form'=>$form->createView()
        ]);
    }
}

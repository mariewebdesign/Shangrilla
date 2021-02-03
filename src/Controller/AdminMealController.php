<?php

namespace App\Controller;


use App\Entity\Meal;
use App\Form\MealType;
use App\Service\Pagination;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMealController extends AbstractController
{
    /**
     * Affichage de la liste des menus
     * @Route("/admin/meals/{page<\d+>?1}", name="admin_meals_list")
     * 
     */
    public function index(Pagination $paginationService, $page)
    {
        $paginationService->setEntityClass(Meal::class)
                          ->setPage($page)
                          //->setRoute('admin_meals_list')
                            ;
        

        
        return $this->render('admin/meal/index.html.twig', [
            'pagination'=>$paginationService  // contient toutes les methodes

            ]);
        }

    /**
     * Permet de modifier une annonce dans la partie admin
     * @Route("admin/meals/{id}/edit",name="admin_meals_edit")
     * 
     * @param Meal $meal
     * @param Request $request
     * @return Response
     */
    public function edit(Meal $meal, Request $request,ObjectManager $manager){

        $form = $this->createForm(MealType::class,$meal);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($meal);
            $manager->flush();

            $this->addFlash('success',"Le menu a bien été modifiée");
        }

        return $this->render('admin/meal/edit.html.twig',[
            'meal'=>$meal,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Suppression d'une annonce
     * @Route("/admin/meals/{id}/delete",name="admin_meals_delete")
     *
     * @param Meal $meal
     * @return Response
     */
    public function delete(Meal $meal,ObjectManager $manager){

        if(count($meal->getOrders()) > 0 ){
            $this->addFlash("warning","Vous ne pouvez pas supprimer un menu qui possède des commandes.");

        }else{
            $manager->remove($meal);
            $manager->flush();

            $this->addFlash("success","Le menu <strong>{$meal->getTitle()}</strong> a bien été supprimé !");
        }

        return $this->redirectToRoute("admin_meals_list");

    }

}
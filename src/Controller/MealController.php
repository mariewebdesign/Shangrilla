<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\MealRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MealController extends AbstractController
{
     /**
     * Permet d'affiche un seul menu
     * @Route("/meals/{slug}",name="meal_single")
     * @return Response
     */
    public function show($slug,Meal $meal){


        return $this->render('meal/show.html.twig',[
            'meal'=>$meal]);

    }

    /**
     * Permet d'afficher une liste de menu
     * @Route("/meals", name="meals_list")
     * @return Response
     */
    public function index(MealRepository $mealRepo){

               
        return $this->render('meal/index.html.twig', [
            'controller_name' => 'Nos menus',
            'meals'=>$mealRepo->findAll()
        ]);
    }

     
}

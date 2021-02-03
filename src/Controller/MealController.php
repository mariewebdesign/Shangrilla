<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Entity\Order;
use App\Form\MealType;
use App\Form\OrderType;
use App\Repository\MealRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

      /**
     * Permet de créer un menu
    * @Route("meals/new",name="meals_create")
    * @IsGranted("ROLE_USER")
    * @return response
    */
    public function create(Request $request,ObjectManager $manager){

        // fabricant de formulaire : FORMBUILDER

        $meal = new Meal();

        // on lance la fabrication et la configuration de notre formulaire
        $form = $this->createForm(MealType::class,$ad);

        // récupération des données du formulaire
        $form -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Si le formulaire est soumis ET si le formulaire est valide, on demande à Doctrine 
            // de sauvegarder ces données dans l'objet manager

            // pour chaque image supplémnentaire ajoutée
            foreach($meal->getCoverImage() as $image){

                // on relie l'image à l'annonce et on modifie l'annonce
                $image->setMeal($meal);

                // on sauvegarde les images

                $manager->persist($image);
            }

            $meal->setAuthor($this->getUser());
            $manager->persist($meal);
            
            $manager->flush();

            $this->addFlash('success',"Menu <strong>{$meal->getTitle()}</strong> créé avec succès");

            return $this->redirectToRoute('meals_single',['slug'=>$meal->getSlug()]);

        }

        return $this->render('meal/new.html.twig',['form'=>$form->createView()]);
    }


    /**
    * Permet d'éditer et de modifier un menu
    * @Route("/meals/{slug}/edit",name="meals_edit")
    * @Security("is_granted('ROLE_USER') and user === meal.getAuthor()",message="Vous n'êtes pas l'auteur de ce menu, vous ne pouvez pas le modifier")
    * @return Response
    */

    public function edit(Meal $meal,Request $request,ObjectManager $manager){

        $form = $this->createForm(MealType::class,$meal);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($meal->getCoverImage() as $image){

                // on relie l'image à l'annonce et on modifie l'annonce
                $image->setMeal($meal);

                // on sauvegarde les images
                $manager->persist($image);
            }

     
            $manager->persist($meal);           
            $manager->flush();

            $this->addFlash("success","les modifications ont été faites !");

            return $this->redirectToRoute('meals_single',['slug'=>$meal->getSlug()]);
        }

        return $this->render('meal/edit.html.twig',['form'=>$form->createView(),'meal'=>$meal]);


    }

    /**
     * Suppression de l'annonce
     * @Route("/meals/{slug}/delete",name="meals_delete")
     * @Security("is_granted('ROLE_USER') and user === meal.getAuthor()",message="Vous n'avez pas le droit d'accéder à cette page !")
     * @param Meal $meal
     * @return Response
     */
    public function delete(Meal $meal,ObjectManager $manager){
        
        $manager->remove($meal);
        $manager->flush();
        $this->addFlash("success","Le menu <em>{$ad->getTitle()}</em> a bien été supprimé");

        return $this->redirectToRoute("account_home");
    }

     
}

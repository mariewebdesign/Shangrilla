<?php

namespace App\Controller;

use App\Service\Statistics;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager,Statistics $statsService)
    {

        $stats = $statsService->getStatistics();
        
        $bestMeals = $statsService->getMealsStats('DESC');
        $worstMeals = $statsService->getMealsStats('ASC');

        
        return $this->render('admin/dashboard/index.html.twig', [
            'stats'=>$stats,
            'bestMeals'=>$bestMeals,
            'worstMeals'=>$worstMeals
        ]);
    }
}

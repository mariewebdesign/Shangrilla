<?php

namespace App\Service;

use Doctrine\Persistence\ObjectManager;

class Statistics{

    public function __construct(ObjectManager $manager){

        $this->manager = $manager;
    }

    public function getStatistics(){

        $users = $this->getUsersCount();
        $meals = $this->getMealsCount();
        $bookings = $this->getBookingsCount();
        $orders = $this->getOrdersCount();
        $comments = $this->getCommentsCount();
        return compact('users','meals','bookings','orders','comments');
    }

    public function getUsersCount(){

        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
        
    }

    public function getMealsCount(){

        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Meal a')->getSingleScalarResult();
     
    }

    public function getBookingsCount(){
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\BookingTable b')->getSingleScalarResult();

    }

    public function getOrdersCount(){
        return $this->manager->createQuery('SELECT COUNT(o) FROM App\Entity\Order o')->getSingleScalarResult();

    }
  

    public function getCommentsCount(){
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getMealsStats($direction){

        return $this->manager->createQuery
            ('SELECT AVG(c.rating) as note,a.title,a.id,u.firstname,u.lastname,u.avatar
             FROM App\Entity\Comment c
             JOIN c.meal a
             JOIN a.author u
             GROUP BY a
             ORDER BY note '.$direction)
             ->setMaxResults(5)
             ->getResult();
        
    }
}
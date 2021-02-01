<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Meal;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Order;
use App\Entity\Comment;
use App\Entity\BookingTable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){

        $this->encoder=$encoder;

    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        // GESTION DES ROLES
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        // Création d'un utilisateur spécial avec un rôle admin

        $adminUser = new User();

        $adminUser->setFirstname('Administrateur')
                  ->setLastname('Shangrila')
                  ->setEmail('admin@shangrila.fr')
                  ->setHash($this->encoder->encodePassword($adminUser,'password'))
                  ->setAvatar('https://randomuser.me/api/portraits/women/55.jpg')
                  ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(5)). '</p>')
                  ->addUserRole($adminRole)
                  ->setAddress($faker->streetAddress)
                  ->setCp($faker->postcode)
                  ->setCity($faker->city)
                  ->setTel($faker->e164PhoneNumber)
                  ;
        
                  $manager->persist($adminUser);


        $users=[];
        $genres=['male','female'];

        // UTILISATEURS
        for($i=1;$i<=10;$i++){
            
            $user = new User();

            $genre =$faker->randomElement($genres);
            $avatar='https://randomuser.me/api/portraits/';
            $avatarId=$faker->numberBetween(1,99).'.jpg';
            $avatar.= ($genre == 'male' ? 'men/' : 'women/') . $avatarId;

            $hash = $this->encoder->encodePassword($user,'password');

            $description = "<p>".join("</p><p>",$faker->paragraphs(1)). "</p>";
            $user->setDescription($description)
                 ->setFirstname($faker->firstname)
                 ->setLastname($faker->lastname)
                 ->setEmail($faker->email)
                 ->setHash($hash)      
                 ->setAvatar($avatar)
                 ->setAddress($faker->streetAddress)
                 ->setCp($faker->postcode)
                 ->setCity($faker->city)
                 ->setTel($faker->e164PhoneNumber)
                ;
            $manager->persist($user);
            $users[]=$user;

        }
       

            // MEAL
                for($i=1; $i<=10;$i++){
                
                $meal = new Meal();
                
                $title = $faker->sentence($nbWords = 2, $variableNbWords = true);
                $coverImage='https://picsum.photos/600/400?random=1';
                $introduction = $faker->paragraph(2);
                $description = "<p>".join("</p><p>",$faker->paragraphs(1)). "</p>";
                $user = $users[mt_rand(0,count($users)-1)];

                $meal->setTitle($title)
                    ->setCoverImage($coverImage)
                    ->setIntroduction($introduction)
                    ->setDescription($description)
                    ->setPrice(mt_rand(5,60))
                   
   
                    ;
                // on sauvegarde
                $manager->persist($meal);
                $meals[]=$meal;
                }
                
                // Génération des commandes de menus
                
                for($k=1;$k <= mt_rand(5,30);$k++){
                    $order = new Order();
                    $createdat = $faker->dateTimeBetween('-6 months');
                    $date = $faker->dateTimeBetween('-3 months');
                    $time = $faker->dateTimeBetween('-3 hours');
                    $meal = $meals[mt_rand(0,count($meals)-1)];
                    $qty = mt_rand(1,12);
                    $amount = $meal->getPrice() * $qty;
                    $payment=1;
                    
    
                    // trouver le client
                    $user = $users[mt_rand(0,count($users)-1)];
        
                      
                    // configuration de la commande
                    $order->setCustomer($user)
                          ->addMeal($meal)
                          ->setDate($date)
                          ->setTime($time)
                          ->setCreatedat($createdat)
                          ->setAmount($amount)
                          ->setQty($qty)
                          ->setPayment($payment)
        
                            ;
    
                    $manager->persist($order);  
                    
                    // Gestion des commentaires

                    if(mt_rand(0,1)){
                        $comment = new Comment();
        
                        $comment->setContent($faker->paragraph())
                                ->setRating(mt_rand(1,5))
                            
                                ->setMeal($meal)
                                ;
                        
                        $manager->persist($comment);
                        
                        }

                    }
            
                    //Réservation de table
                    for($y=1;$y <= mt_rand(0,100);$y++){
                        $bookingtable = new BookingTable();
                        $createdat = $faker->dateTimeBetween('-6 months');
                        $date = $faker->dateTimeBetween('-3 months');
                        $time = $faker->numberBetween($min = 1900, $max = 2130);
                        $customers = $faker->numberBetween($min = 1, $max = 12);
        
                        // trouver le client
                        $customer = $users[mt_rand(0,count($users)-1)];
                          
                        // configuration de la commande
                        $bookingtable->setUsers($customer)
                              ->setDate($date)
                              ->setTime($time)
                              ->setCreatedat($createdat)
                              ->setCustomers($customers)
                                ;
        
                        $manager->persist($bookingtable); 
                        
                        
    
            }

        $manager->flush();
    }

}
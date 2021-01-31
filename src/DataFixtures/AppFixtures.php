<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
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

           
            

            $description = "<p>".join("</p><p>",$faker->paragraphs(5)). "</p>";
            $user->setDescription($description)
                 ->setFirstname($faker->firstname)
                 ->setLastname($faker->lastname)
                 ->setEmail($faker->email)
                 ->setHash($hash)      
                 ->setAvatar($avatar)
                ;
            $manager->persist($user);
            $users[]=$user;

        }

        $manager->flush();
    }
}

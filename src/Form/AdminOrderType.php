<?php

namespace App\Form;


use App\Entity\Meal;
use App\Entity\User;
use App\Entity\Order;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\DataTransformer\FrToDatetimeTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminOrderType extends ApplicationType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',DateType::class,$this->getConfiguration("Date de la commande"," ",
            ['widget'=>'single_text','attr' => ['class'=>'center']]))
            ->add('comment',TextareaType::class,['label'=>'Commentaire client'])
            ->add('customer',EntityType::class,[
                'class'=>User::class,
                'choice_label'=>function($user){
                    return $user->getFirstname()." ".strtoupper($user->getlastname());
                    },
                'label'=>'Visiteur'
                ])
            ->add('meal',EntityType::class,[
                'class'=>Meal::class,
                'choice_label'=>function($meal){
                    return $meal->getId()." - ".$meal->getTitle();
                    },
                'label'=>'Menu'
                ])
            ;
 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'validation_groups'=>['Default']
        ]);
    }
}

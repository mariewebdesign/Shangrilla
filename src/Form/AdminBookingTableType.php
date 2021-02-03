<?php

namespace App\Form;


use App\Entity\User;
use App\Entity\BookingTable;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\DataTransformer\FrToDatetimeTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AdminBookingTableType extends ApplicationType
{

  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('date',DateType::class,$this->getConfiguration("Date de la réservation "," ",
        ['widget'=>'single_text','attr' => ['class'=>'center']]))
            ->add('time',ChoiceType::class,
                ['choices' => [
                    '19h00' => '19h00',
                    '19h15' => '19h15',
                    '19h30' => '19h30',
                    '19h45' => '19h45',
                    '20h00' => '20h00',
                    '20h15' => '20h15',
                    '20h30' => '20h30',
                    '20h45' => '20h45',
                    '21h00' => '21h00',
                    '21h15' => '21h15',
                    '21h30' => '21h30'
                ],
                'label' => 'Heure de la réservation ',
                'attr' => ['class'=>'center']])
            ->add('users',EntityType::class,[
                'class'=>User::class,
                'choice_label'=>function($user){
                    return $user->getFirstname()." ".strtoupper($user->getlastname());
                    },
                'label'=>'Visiteur'
                ])
            ;
  
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BookingTable::class,
            'validation_groups'=>['Default']
        ]);
    }
}

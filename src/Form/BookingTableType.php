<?php

namespace App\Form;

use App\Entity\BookingTable;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class BookingTableType extends ApplicationType
{


    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',DateType::class,$this->getConfiguration(" "," ",
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
                'label' => ' ',
                'attr' => ['class'=>'center']])
            ->add('customers',IntegerType::class,$this->getConfiguration(" "," ",
                ['attr'=>['min'=>0,
                          'max'=>12],
                'attr' => ['class'=>'center']
                ]))
        
        ;

       

       
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BookingTable::class,
        ]);
    }
}

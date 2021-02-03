<?php

namespace App\Form;

use App\Entity\Meal;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MealType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title',TextType::class,$this->getConfiguration('Titre','Insérer un titre'))
        ->add('slug',TextType::class,$this->getConfiguration('Alias','Personnalisez un alias pour générer l\'url',['required'=>false]))
        ->add('coverImage',UrlType::class,$this->getConfiguration('Image de couverture','Url de l\'image'))
        ->add('introduction',TextType::class,$this->getConfiguration('Résumé','Présentez votre bien'))
        ->add('description',TextareaType::class,$this->getConfiguration('Description détaillée','Décrivez vos services'))
        ->add('price',MoneyType::class,$this->getConfiguration('Prix','Prix des chambres/nuit'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Meal::class,
        ]);
    }
}

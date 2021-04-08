<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;

class PreventaInternetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dni')                
            ->add('nombre')
            ->add('email', Type\EmailType::class, array(
                'required' => false,
            ))                         
            ->add('telefono')                           
            ->add('direccion')                  
            ->add('observacion')                        
        ;
    }

    public function getName()
    {
        return 'preventa_internet';
    }       
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PreventaInternet',
            'validation_groups' => array('Default', 'preventa'),
        ));        
    }  
}

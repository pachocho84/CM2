<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventDateType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', 'datetime', array(
                'date_widget'       => 'single_text',
                'time_widget'       => 'single_text',
                'model_timezone'    => 'GMT',
                'view_timezone'     => 'Europe/Rome'
            ))
            ->add('end', 'datetime', array(
                'required'            => false,
                'date_widget'       => 'single_text',
                'time_widget'       => 'single_text',
                'model_timezone'    => 'GMT',
                'view_timezone'     => 'Europe/Rome',
            ))
            ->add('address', 'text', array(
                'attr' => array('address-autocomplete' => '')
            ))
            ->add('location', 'text', array(
                'attr' => array('places-autocomplete' => '')
            ))
            ->add('coordinates', 'hidden', array('attr' => array('address-coordinates' => '')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\EventDate',
            'widget_control_group' => false,
            'widget_controls' => false,
            'attr' => array('class' => 'event_date')
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_eventdate';
    }
}

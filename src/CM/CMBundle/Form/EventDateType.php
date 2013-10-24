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
                'attr' => array('class' => 'col-lg-9'),
                'date_widget'       => 'single_text',
/*                 'date_format'       => \IntlDateFormatter::SHORT, */
                'time_widget'       => 'single_text',
                'model_timezone'    => 'GMT',
                'view_timezone'     => 'Europe/Rome'
            ))
            ->add('end', 'datetime', array(
                'attr' => array('class' => 'col-lg-9'),
                'required'            => false,
                'date_widget'       => 'single_text',
/*                 'date_format'       => \IntlDateFormatter::SHORT, */
                'time_widget'       => 'single_text',
                'model_timezone'    => 'GMT',
                'view_timezone'     => 'Europe/Rome',
            ))
            ->add('location')
            ->add('address')
            ->add('coordinates', 'text', array(
                'required' => false
            ));
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

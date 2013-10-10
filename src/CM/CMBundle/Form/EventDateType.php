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
/*                 'date_format'       => \IntlDateFormatter::SHORT, */
                'time_widget'       => 'single_text',
                'model_timezone'    => 'GMT',
                'view_timezone'     => 'Europe/Rome',
            ))
            ->add('end', 'datetime', array(
                'required'            => false,
                'date_widget'       => 'single_text',
/*                 'date_format'       => \IntlDateFormatter::SHORT, */
                'time_widget'       => 'single_text',
                'model_timezone'    => 'GMT',
                'view_timezone'     => 'Europe/Rome',
            ))
            ->add('location')
            ->add('address')
            ->add('coordinates');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\EventDate'
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

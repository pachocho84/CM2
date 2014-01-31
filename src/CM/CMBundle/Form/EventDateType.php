<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Service\Helper;
use CM\CMBundle\Form\DataTransformer\DateTimeToTextTransformer;

class EventDateType extends AbstractType
{
    private $intl;
    
    private $helper;

    public function __construct($intl, Helper $helper)
    {
        $this->intl = $intl;
        $this->helper = $helper;
    }

     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder->create('start', 'text', array('attr' => array('datetimepicker' => '')))->addModelTransformer(new DateTimeToTextTransformer($this->intl, $this->helper)))
            ->add($builder->create('end', 'text', array('attr' => array('datetimepicker' => '')))->addModelTransformer(new DateTimeToTextTransformer($this->intl, $this->helper)))
            // ->add('end', 'datetime', array(
            //     'attr' => array('datetimepicker' => '')
            // ))
            ->add('location', 'text', array(
                'attr' => array('places-autocomplete' => '')
            ))
            ->add('address', 'text', array(
                'attr' => array('address-autocomplete' => '')
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

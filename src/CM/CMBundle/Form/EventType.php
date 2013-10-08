<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;

class EventType extends EntityType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	parent::buildForm($builder, $options);
    
    	$builder->add('event_dates', 'collection', array(
    			'type' => new EventDateType(),
    			'allow_add' => true,
    			'by_reference' => false
    	))
    		->add('posts', 'collection', array(
    			'type' => new PostType(),
    			'by_reference' => false
    	));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	parent::setDefaultOptions($resolver);
    	
/*
    	      	$event = new Event;
			$event->addEventDate(new EventDate);
			$image = new Image;
			$image->setMain(true);
			$event->addImage($image);
*/
    	
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Event',
/*             'empty_data' => new Event(new EventDate) */
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_event';
    }
}

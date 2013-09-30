<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->
add('translations', 'a2lix_translations_gedmo', array(
		    'translatable_class' => "CM\CMBundle\Entity\Event",
		    'fields' => array(
		        'title' => array(),
		        'slug' => array('display' => false ),
		        'subtitle' => array(),
		        'extract' => array(),
		        'text' => array()
		    )
		))
/*
			->add('title')
			->add('subtitle')
			->add('extract')
			->add('text')
*/
			->add('visible');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Event'
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

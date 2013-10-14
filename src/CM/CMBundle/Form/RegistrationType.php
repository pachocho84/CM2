<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationType extends BaseType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	parent::buildForm($builder, $options);
    
        $builder
        	->add('firstName')
            ->add('lastName')
            ->add('sex', 'choice', array(
				'choices' => array('M' => 'Male', 'F' => 'Female'),
				'expanded' => true
			))
			->add('cityBirth', 'text', array('label' => 'City of birth'))
			->add('cityCurrent', 'text', array('label' => 'Current city'))
			->add('birthDate', 'date', array(
				'widget' => 'single_text'
			))
			->add('birthDateVisible', 'choice', array(
				'choices' => array(true => 'Visible', false => 'Not visible'),
				'expanded' => true
			))
			->add('file');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_user_registration';
    }
}

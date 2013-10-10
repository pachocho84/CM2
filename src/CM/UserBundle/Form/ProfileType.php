<?php

namespace CM\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use CM\UserBundle\Entity\User;

class ProfileType extends BaseType
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
				'choices' => array(User::SEX_M => 'Male', User::SEX_F => 'Female'),
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
            'data_class' => 'CM\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_userbundle_user_profile';
    }
}

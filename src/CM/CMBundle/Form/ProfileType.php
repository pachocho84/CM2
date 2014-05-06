<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use CM\CMBundle\Entity\User;

class ProfileType extends BaseType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // parent::buildForm($builder, $options);
    
        $builder->add('firstName')
            ->add('lastName')
            ->add('sex', 'choice', array(
                'choices' => array(User::SEX_M => 'Male', User::SEX_F => 'Female'),
                'expanded' => true
            ))
            ->add('type', 'choice', array(
                'choices' => array(
                    User::TYPE_PROFESSIONAL => 'Professional musician',
                    User::TYPE_STUDENT => 'Musical student',
                    User::TYPE_KEEN => 'Keen about music'),
                'expanded' => true,
                'error_bubbling' => false,
            ))
            ->add('cityBirth', 'text', array(
                'label' => 'City of birth',
                'attr' => array('city-autocomplete' => '')
            ))
            ->add('cityBirthLang', 'hidden', array('attr' => array('city-lang' => '')))
            ->add('cityBirthLatitude', 'hidden', array('attr' => array('city-latitude' => '')))
            ->add('cityBirthLongitude', 'hidden', array('attr' => array('city-longitude' => '')))
            ->add('cityCurrent', 'text', array(
                'label' => 'Current city',
                'attr' => array('city-autocomplete' => '')
            ))
            ->add('cityCurrentLang', 'hidden', array('attr' => array('city-lang' => '')))
            ->add('cityCurrentLatitude', 'hidden', array('attr' => array('city-latitude' => '')))
            ->add('cityCurrentLongitude', 'hidden', array('attr' => array('city-longitude' => '')))
            ->add('birthDate', 'date', array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') - 110)
            ))
            ->add('birthDateVisible', 'choice', array(
                'choices' => array(
                    User::BIRTHDATE_VISIBLE => 'Visible',
                    User::BIRTHDATE_NO_YEAR => 'Not visible',
                    User::BIRTHDATE_INVISIBLE => 'Year not visible'),
                'expanded' => true
            ));
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
        return 'cm_cmbundle_user_profile';
    }
}

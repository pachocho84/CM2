<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as EWZ;
use CM\CMBundle\Entity\User;

class RegistrationType extends BaseType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    
        $builder->add('firstName')
            ->add('lastName')
            ->add('sex', 'choice', array(
                'choices' => array(User::SEX_M => 'Male', User::SEX_F => 'Female'),
                'expanded' => true
            ))
            ->add('imgFile')
            ->add('recaptcha', 'ewz_recaptcha', array(
                'mapped' => false,
                'constraints'   => array(
                    new EWZ\True
                )
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
        return 'cm_cmbundle_user_registration';
    }
}

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
            ->add('type', 'choice', array(
                'choices' => array(
                    User::TYPE_PROFESSIONAL => 'Professional musician',
                    User::TYPE_STUDENT => 'Musical student',
                    User::TYPE_KEEN => 'Keen about music'),
                'expanded' => true,
                'error_bubbling' => false,
            ))
            ->add('imgFile')
            ->add('imgOffset', 'hidden')
            ->add('birthDate', 'date', array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') - 110),
                'error_bubbling' => false,
            ))
            ->add('birthDateVisible', 'choice', array(
                'choices' => array(
                    User::BIRTHDATE_VISIBLE => 'Visible',
                    User::BIRTHDATE_NO_YEAR => 'Not visible',
                    User::BIRTHDATE_INVISIBLE => 'Year not visible'),
                'expanded' => true,
                'error_bubbling' => false,
            ))
            ->add('recaptcha', 'ewz_recaptcha', array(
                'required' => true,
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

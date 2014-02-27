<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\Education;

class EducationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    
        $builder->add('school')
            ->add('course')
            ->add('teacher')
            ->add('dateFrom', 'date')
            ->add('dateTo', 'date')
            ->add('mark')
            ->add('markScale', 'choice', array('choices' => array(
                Education::MARK_SCALE_10 => '10',
                Education::MARK_SCALE_30 => '30',
                Education::MARK_SCALE_60 => '60',
                Education::MARK_SCALE_100 => '100',
            )))
            ->add('laude')
            ->add('honour')
            ->add('courseType')
            ->add('degreeType')
            ->add('description');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'prototype' => true,
            'data_class' => 'CM\CMBundle\Entity\Education',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_education';
    }
}

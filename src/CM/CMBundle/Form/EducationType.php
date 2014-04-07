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
        $builder->add('school')
            ->add('course')
            ->add('teacher')
            ->add('dateFrom', 'date', array('required' => false, 'widget' => 'single_text', 'empty_value' => ''))
            ->add('dateTo', 'date', array('required' => false, 'widget' => 'single_text', 'empty_value' => ''))
            ->add('mark')
            ->add('markScale', 'choice', array('required' => false, 'choices' => array(
                Education::MARK_SCALE_10 => '10',
                Education::MARK_SCALE_30 => '30',
                Education::MARK_SCALE_60 => '60',
                Education::MARK_SCALE_100 => '100',
            )))
            ->add('laude')
            ->add('honour')
            ->add('courseType')
            ->add('degreeType')
            ->add('description', 'textarea', array('attr' => array('expandable' => $options['expandable'])));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Education',
            'expandable' => ''
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

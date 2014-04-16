<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\Work;

class WorkType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('position', 'text', array('required' => true))
            ->add('company')
            ->add('dateFrom', 'date', array('required' => false, 'widget' => 'single_text', 'empty_value' => ''))
            ->add('dateTo', 'date', array('required' => false, 'widget' => 'single_text', 'empty_value' => ''))
            ->add('description', 'textarea', array('required' => false, 'attr' => array('expandable' => is_null($builder->getData()['description']) ? 'small' : '')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Work'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_work';
    }
}

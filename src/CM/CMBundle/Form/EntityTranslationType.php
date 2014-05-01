<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityTranslationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['title']) {
            $builder->add('title', 'text', array('error_bubbling' => false));
        }
        $builder->add('text', 'textarea', array(
            'error_bubbling' => false,
            'attr' => array('class' => $options['articleWriter'] ? 'tinymce-advanced' : '', 'expandable' => is_null($builder->getData()) ? $options['expandable_small'] : ''),
            'label' => 'Description'
        ));
        if ($options['articleWriter']) {
            $builder->add('extract', 'textarea', array('error_bubbling' => false, 'attr' => array('class' => 'tinymce')));
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'articleWriter' => false,
            'error_bubbling' => false,
            'data_class' => 'CM\CMBundle\Entity\EntityTranslation',
            'title' => true,
            'expandable_small' => 'small'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_entity_translation';
    }
}

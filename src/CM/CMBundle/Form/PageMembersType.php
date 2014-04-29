<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;

class PageMembersType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pageUsers', 'collection', array(
                'type' => new $options['type'],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options' => array(
                    'tags' => $options['tags'],
            )));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Page',
            'tags' => array(),
            'type' => null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_pagemembers';
    }
}

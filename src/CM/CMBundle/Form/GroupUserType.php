<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\GroupUser;

class GroupUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    
        $builder->add('joinArticle', 'choice', array(
                'expanded' => true,
                'choices' => array(GroupUser::JOIN_NO, GroupUser::JOIN_YES, GroupUser::JOIN_REQUEST)
            ))->add('joinDisc', 'choice', array(
                'expanded' => true,
                'choices' => array(GroupUser::JOIN_NO, GroupUser::JOIN_YES, GroupUser::JOIN_REQUEST)
            ))->add('joinEvent', 'choice', array(
                'expanded' => true,
                'choices' => array(GroupUser::JOIN_NO, GroupUser::JOIN_YES, GroupUser::JOIN_REQUEST)
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\GroupUser',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_groupUser';
    }
}

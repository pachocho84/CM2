<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Form\DataTransformer\TagsToArrayTransformer;
use CM\CMBundle\Entity\PageUser;

class PageUserJoinType extends AbstractType
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
                'choices' => array(PageUser::JOIN_NO, PageUser::JOIN_YES, PageUser::JOIN_REQUEST)
            ))->add('joinDisc', 'choice', array(
                'expanded' => true,
                'choices' => array(PageUser::JOIN_NO, PageUser::JOIN_YES, PageUser::JOIN_REQUEST)
            ))->add('joinEvent', 'choice', array(
                'expanded' => true,
                'choices' => array(PageUser::JOIN_NO, PageUser::JOIN_YES, PageUser::JOIN_REQUEST)
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {        
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\PageUser',
            'tags' => null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_pageuser';
    }
}

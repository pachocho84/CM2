<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\UserTagRepository;
use CM\CMBundle\Form\DataTransformer\UserToIntTransformer;
use CM\CMBundle\Form\DataTransformer\TagsToTextTransformer;

class UserTagsType extends AbstractType
{
    protected $options;

    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
    }
        
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add($builder->create('userTags', 'hidden', array(
                    'attr' => array('tags' => array_reduce($options['tags'], function($carry, $a) { return $carry.(is_null($carry) ? '' : ';').$a->getId().','.$a; })),
                    'label' => 'Roles'
                ))->addModelTransformer(new TagsToTextTransformer($options['tags'], 'CM\CMBundle\Entity\UserTag')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'tags' => array(),
            'data_class' => 'CM\CMBundle\Entity\User'
        ));

        $resolver->setRequired(array(
            'tags'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_usertags';
    }
}

<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Form\DataTransformer\UserToIntTransformer;
use CM\CMBundle\Form\DataTransformer\TagsToArrayTransformer;
use CM\CMBundle\Entity\PageUser;

class PageUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    
        $builder->add($builder->create('user', 'hidden')->addModelTransformer(new UserToIntTransformer($options['em'])))
            ->add($builder->create('pageUserTags', 'choice', array(
                    'attr' => array('tags' => ''),
                    'choices' => $options['tags'],
                    'multiple' => true,
                    'by_reference' => false,
                    'label' => 'Roles'
                ))->addModelTransformer(new TagsToArrayTransformer($options['tags'], 'CM\CMBundle\Entity\PageUserTag')
            ))->add('admin', 'checkbox', array(
                'required' => false,
                'label' => 'Make admin'
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {        
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\PageUser',
            'tags' => array(),
        ));

        $resolver->setRequired(array(
            'em',
            'tags'
        ));

        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_pageusertags';
    }
}

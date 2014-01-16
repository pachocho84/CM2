<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\EntityCategoryRepository;
use CM\CMBundle\Entity\Image;

class EntityUserCollectionType extends AbstractType
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
        $builder->add('entityUsers', 'collection', array(
                'type' => new EntityUserType,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options' => array(
                    'em' => $options['em'],
                    'roles' => $options['roles'],
                    'tags' => $options['user_tags'],
                    'locale' => $options['locale'],
                    'locales' => $options['locales'],
                )
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'roles' => array(),
            'tags' => array(),
            'locale' => 'en',
            'locales' => array('en'),
            'data_class' => null
        ));

        $resolver->setRequired(array(
            'em',
            'roles'
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
        return 'cm_cmbundle_entityuser_collection';
    }
}

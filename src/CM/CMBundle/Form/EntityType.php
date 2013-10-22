<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\EntityCategoryRepository;
use CM\CMBundle\Entity\Image;

class EntityType extends AbstractType
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
        $builder->add('translations', 'a2lix_translations', array(
                'locales' => $options['locales'],
                'required' => true,    
                'fields' => array(
                    'title' => array(),
                    'subtitle' => array(
                        'required' => false
                    ),
                    'extract' => array(
                        'required' => false
                    ),
                    'text' => array(),
                    'slug' => array('display' => false)
                )
            ))
            ->add('entityCategory', 'entity', array(
                'class' => 'CMBundle:EntityCategory',
                'query_builder' => function(EntityCategoryRepository $er) use ($options) {
                    // get Entity child class name, to retrieve the EntityCategoty type associated
                    $entityChild = strtoupper(preg_replace('/^[\w\d_\\\]*\\\/', '', rtrim(get_class($this), 'Type')));
                    $entityCategory = constant('CM\CMBundle\Entity\EntityCategory::'.$entityChild);
                    return $er->filterEntityCategoriesByEntityType($entityCategory, $options);
                }
            ))/*
            ->add('entityUsers', 'collection', array(
                'type' => new EntityUserType,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'options' => array(
                    'tags' => $options['user_tags'],
                    'locale' => $options['locale'],
                    'locales' => $options['locales'],
                    'label_render' => false,
                )
            ))*/
            ->add('visible')
/*
            ->add('images', 'collection', array(
                'type' => new ImageType,
                'by_reference' => false,
                'options' => array(
                    'label_render' => false,
                )
            ))
*/;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => array('class' => 'form-horizontal'),
            'user_tags' => array(),
            'locale' => 'en',
            'locales' => array('en'),
            'data_class' => 'CM\CMBundle\Entity\Entity'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_entity';
    }
}

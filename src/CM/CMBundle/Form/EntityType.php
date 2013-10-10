<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\EntityCategoryRepository;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\EntityCategoryEnum;

class EntityType extends AbstractType
{
    protected $options;

    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        $this->options = $resolver->resolve($options);
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
            ->add('entity_category', 'entity', array(
            'class' => 'CMBundle:EntityCategory',
            'query_builder' => function(EntityCategoryRepository $er) use ($options) {
                // get Entity child class name, to retrieve the EntityCategoty type associated
                $entityChild = strtoupper(preg_replace('/^[\w\d_\\\]*\\\/', '', rtrim(get_class($this), 'Type')));
                $entityCategory = constant('CM\CMBundle\Entity\EntityCategory::'.$entityChild);
                return $er->filterEntityCategoriesByEntityType($entityCategory, $options);
            }
        ))
            ->add('visible')
            ->add('images', 'collection', array(
                'type' => new ImageType(),
                'by_reference' => false
        ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
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

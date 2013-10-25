<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\EntityCategoryRepository;
use CM\CMBundle\Entity\EntityTranslation;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Form\EntityTranslationType;
use CM\CMBundle\Form\DataTransformer\ArrayCollectionToEntityTransformer;

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
        if (in_array('ROLE_CLIENT', $options['roles'])) {
            $builder->add('translations', 'a2lix_translations', array(
                    'locales' => $options['locales'],
                    'required' => true,
                    'fields' => array(
                        'title' => array(),
                        'subtitle' => array(
                            'display' => in_array('ROLE_CLIENT', $options['roles']),
                            'required' => false,
                        ),
                        'extract' => array(
                            'display' => in_array('ROLE_CLIENT', $options['roles']),
                            'required' => false
                        ),
                        'text' => array(),
                        'slug' => array('display' => false)
                    )
                ));
        } else {
            $builder->add($builder->create('translations', new EntityTranslationType, array('label' => 'Body'))->addModelTransformer(new ArrayCollectionToEntityTransformer($options['em'], 'en')));

            // $builder->add('translations', 'collection', array(
            //         'type' => new EntityTranslationType
            //     ));
        }
        $builder->add('entityCategory', 'entity', array(
                'label' => 'Category',
                'class' => 'CMBundle:EntityCategory',
                'query_builder' => function(EntityCategoryRepository $er) use ($options) {
                    // get Entity child class name, to retrieve the EntityCategoty type associated
                    $entityChild = strtoupper(preg_replace('/^[\w\d_\\\]*\\\/', '', rtrim(get_class($this), 'Type')));
                    $entityCategory = constant('CM\CMBundle\Entity\EntityCategory::'.$entityChild);
                    return $er->filterEntityCategoriesByEntityType($entityCategory, $options);
                }
            ))
            ->add('entityUsers', 'collection', array(
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
        if (in_array('ROLE_CLIENT', $options['roles'])) {
            $builder->add('visible');
        }
        $builder->add('images', 'collection', array(
                'type' => new ImageType,
                'by_reference' => false,
                'options' => array(
                    'error_bubbling' => false,
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
            'attr' => array('class' => 'form-horizontal'),
            'user_tags' => array(),
            'locale' => 'en',
            'locales' => array('en'),
            'data_class' => 'CM\CMBundle\Entity\Entity'
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
        return 'cm_cmbundle_entity';
    }
}

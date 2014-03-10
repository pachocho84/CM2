<?php

namespace CM\CMBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use CM\CMBundle\Entity\EntityCategoryRepository;
use CM\CMBundle\Entity\Image;

abstract class EntityAdmin extends Admin;
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('translations', 'a2lix_translations', array(
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
            ->add('category', 'entity', array(
                'class' => 'CMBundle:EntityCategory',
                'query_builder' => function(CategoryRepository $er) use ($options) {
                    // get Entity child class name, to retrieve the EntityCategoty type associated
                    $entityChild = strtoupper(preg_replace('/^[\w\d_\\\]*\\\/', '', rtrim(get_class($this), 'Type')));
                    $category = constant('CM\CMBundle\Entity\EntityEntityCategory::'.$entityChild);
                    return $er->filterEntityCategoriesByEntityType($category, $options);
                }
            ))
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
            ))
            ->add('visible')
            ->add('images', 'collection', array(
                'type' => new ImageType,
                'by_reference' => false,
                'options' => array(
                    'label_render' => false,
                )
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('translations', 'a2lix_translations', array(
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
                    'slug' => array('display' => false)
                )
            ))
            ->add('category', 'entity', array(
                'class' => 'CMBundle:EntityCategory',
                'query_builder' => function(CategoryRepository $er) use ($options) {
                    // get Entity child class name, to retrieve the EntityCategoty type associated
                    $entityChild = strtoupper(preg_replace('/^[\w\d_\\\]*\\\/', '', rtrim(get_class($this), 'Admin')));
                    $category = constant('CM\CMBundle\Entity\EntityEntityCategory::'.$entityChild);
                    return $er->filterEntityCategoriesByEntityType($category, $options);
                }
            ))
            ->add('visible');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('translations', 'a2lix_translations', array(
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
            ->add('category', 'entity', array(
                'class' => 'CMBundle:EntityCategory',
                'query_builder' => function(CategoryRepository $er) use ($options) {
                    // get Entity child class name, to retrieve the EntityCategoty type associated
                    $entityChild = strtoupper(preg_replace('/^[\w\d_\\\]*\\\/', '', rtrim(get_class($this), 'Type')));
                    $category = constant('CM\CMBundle\Entity\EntityEntityCategory::'.$entityChild);
                    return $er->filterEntityCategoriesByEntityType($category, $options);
                }
            ))
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
            ))
            ->add('visible')
            ->add('images', 'collection', array(
                'type' => new ImageType,
                'by_reference' => false,
                'options' => array(
                    'label_render' => false,
                )
            ));
    }
}
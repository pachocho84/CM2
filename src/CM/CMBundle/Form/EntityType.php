<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use CM\CMBundle\Entity\Entity;
use CM\CMBundle\Entity\EntityCategoryRepository;
use CM\CMBundle\Entity\EntityTranslation;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Form\EntityTranslationType;
use CM\CMBundle\Form\DataTransformer\ArrayCollectionToEntityTransformer;

class EntityType extends BaseEntityType
{
    protected $options;

    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder->add('category', 'entity', array(
                'label' => 'Select a category',
                'class' => 'CMBundle:EntityCategory',
                'query_builder' => function(EntityCategoryRepository $er) use ($options) {
                    // get Entity child class name, to retrieve the EntityCategoty type associated
                    $entityChild = strtoupper(preg_replace('/^[\w\d_\\\]*\\\/', '', preg_replace('/Type$/', '', get_class($this))));
                    $category = constant('CM\CMBundle\Entity\EntityCategory::'.$entityChild);
                    return $er->filterEntityCategoriesByEntityType($category, $options);
                }
            // ))->add('images', 'collection', array(
            //     'type' => new ImageType,
            //     'by_reference' => false,
            //     'options' => array(
            //         'error_bubbling' => false,
            //     )
            // ))->add('multimedia', 'collection', array(
            //     'type' => new MultimediaType,
            //     'by_reference' => false,
            //     'options' => array(
            //         'error_bubbling' => false,
            //     )
            ))->add('image', new ImageType, array(
            ))->add('imgOffset', 'hidden', array(
                'attr' => array('img-offset-field' => '')
            ))->add('entityUsers', 'collection', array(
                'type' => new EntityUserType,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options' => array(
                    'em' => $options['em'],
                    'roles' => $options['roles'],
                    'tags' => $options['tags'],
                    'locale' => $options['locale'],
                    'locales' => $options['locales'],
                )
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $form = $event->getForm();
            $data = $event->getData();

            $pages = $options['em']->getRepository('CMBundle:Page')->getPagesForUser($options['userId'], array('isAdmin' => true));
            $choices = array();
            foreach ($pages as $page) {
                $choices[$page->getId()] = $page->__toString();
            }

            if ($data instanceof Entity && count($pages) > 0) {
                $form->add('pages', 'choice', array(
                    'required' => false,
                    'empty_value' => 'no page',
                    'mapped' => false,
                    'expanded' => false,
                    'multiple' => false,
                    'choices' => $choices
                ));
            }
         });
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title' => true,
            'userId' => null,
            'roles' => array(),
            'tags' => array(),
            'locale' => 'en',
            'locales' => array('en'),
            'data_class' => 'CM\CMBundle\Entity\Entity'
        ));

        $resolver->setRequired(array(
            'em',
            'userId',
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

<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\EntityCategoryRepository;
use CM\CMBundle\Entity\EntityTranslation;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Form\EntityTranslationType;
use CM\CMBundle\Form\DataTransformer\ArrayCollectionToEntityTransformer;

class BaseEntityType extends AbstractType
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
                    'locales' => array('en', 'it'), // FIXME: $options['locales'],
                    'required' => true,
                    'fields' => array(
                        'title' => $options['title'] ? array() : array(
                            'display' => false
                        ),
                        'subtitle' => $options['title'] ? array(
                            'required' => false,
                        ) : array(
                            'display' => false
                        ),
                        'extract' => array(
                            'attr' => array(
                                'class' => 'tinymce',
                            ),
                            'required' => false
                        ),
                        'text' => array(
                            // 'type' => 'textarea',
                            'attr' => array(
                                'class' => 'tinymce-advanced',
                            ),
                            'label' => 'description'
                        ),
                        'slug' => array('display' => false)
                    )
                ));
        } else {
            $builder->add($builder->create('translations', new EntityTranslationType, array('error_bubbling' => false, 'articleWriter' => $options['articleWriter']))
                ->addModelTransformer(new ArrayCollectionToEntityTransformer($options['em'], 'en')))
                ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
                    $data = $event->getData();
                    if (isset($data['translations']['title'])) {
                        $data['translations']['title'] = htmlentities($data['translations']['title']);
                    }
                    if (isset($data['translations']['subtitle'])) {
                        $data['translations']['subtitle'] = htmlentities($data['translations']['subtitle']);
                    }
                    if (isset($data['translations']['extract'])) {
                        $data['translations']['extract'] = htmlentities($data['translations']['extract']);
                    }
                    if (isset($data['translations']['text'])) {
                        $data['translations']['text'] = htmlentities($data['translations']['text']);
                    }
                    $event->setData($data);
                });
        }
        if (in_array('ROLE_ADMIN', $options['roles'])) {
            $builder->add('post', new PostType, array('label' => 'Post'));
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title' => true,
        	'em' => null,
            'roles' => array(),
            'user_tags' => array(),
            'locale' => 'en',
            'locales' => array('en'),
            'articleWriter' => false,
            'add_category' => true,
            'add_users' => true,
            'data_class' => 'CM\CMBundle\Entity\Entity'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_baseentity';
    }
}

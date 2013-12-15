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
                    'locales' => $options['locales'],
                    'required' => true,
                    'fields' => array(
                        'title' => array(),
                        'subtitle' => array(
                            'required' => false,
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
                            )
                        ),
                        'slug' => array('display' => false)
                    )
                ));
        } else {
            $builder->add($builder->create('translations', new EntityTranslationType, array('label' => 'Body',
                    'error_bubbling' => false,))->addModelTransformer(new ArrayCollectionToEntityTransformer($options['em'], 'en')));

            // $builder->add('translations', 'collection', array(
            //         'type' => new EntityTranslationType
            //     ));
        }
        if (in_array('ROLE_CLIENT', $options['roles'])) {
            $builder->add('visible');
        }
        if (in_array('ROLE_ADMIN', $options['roles'])) {
            $builder->add($builder->create('posts', new PostType, array('label' => 'Post'))->addModelTransformer(new ArrayCollectionToEntityTransformer($options['em'])));
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        	'em' => null,
            'roles' => array(),
            'user_tags' => array(),
            'locale' => 'en',
            'locales' => array('en'),
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

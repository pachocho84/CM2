<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Form\DataTransformer\HomepageCategoryToHomepageArchiveTransformer;
use CM\CMBundle\Entity\HomepageCategoryRepository;

class ArticleType extends EntityType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['articleWriter'] = in_array('ROLE_WRITER', $options['roles']);
        parent::buildForm($builder, $options);
    
        if (in_array('ROLE_WRITER', $options['roles'])) {
            $builder->add('homepage', 'checkbox', array('required' => false));
            $builder->add($builder->create('homepageArchive', 'entity', array(
                    'required' => false,
                    'label' => 'Category',
                    'class' => 'CMBundle:HomepageCategory',
                    'query_builder' => function(HomepageCategoryRepository $er) use ($options) {
                        return $er->filterHomepageCategories($options);
                    }
                ))
                ->addModelTransformer(new HomepageCategoryToHomepageArchiveTransformer($options['em'], 'en')));
        }
        $builder->add('source')
            ->add('date', 'date', array(
                'widget' => 'single_text',
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Article',
            'groups' => array('Article', 'Default'),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_article';
    }
}

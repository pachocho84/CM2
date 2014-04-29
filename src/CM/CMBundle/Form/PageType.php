<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\Page;
use CM\CMBundle\Form\DataTransformer\TagsToArrayTransformer;

class PageType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add($builder->create('pageTags', 'choice', array(
                    'attr' => array('tags' => ''),
                    'choices' => $options['tags'],
                    'multiple' => true,
                    'by_reference' => false,
                    'label' => 'Types'
                ))->addModelTransformer(new TagsToArrayTransformer($options['tags'], 'CM\CMBundle\Entity\PageTag')))
            ->add('biography', new BiographyType, array(
                'roles' => $options['roles'],
                'em' => $options['em'],
                'locale' => $options['locale'],
                'locales' => $options['locales']
            ))->add('website')
            ->add('imgFile', 'file', array(
                'attr' => array('image' => ''),
                'label'  => 'Image'
            ))->add('imgOffset', 'hidden', array(
                'attr' => array('img-offset-field' => '')
            ))
            ->add('coverImgFile', 'file', array(
                'attr' => array('image' => ''),
                'label'  => 'Image'
            ))->add('coverImgOffset', 'hidden', array(
                'attr' => array('img-offset-field' => '')
            ));
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
            'data_class' => 'CM\CMBundle\Entity\Page',
            'tags' => array(),
            'em' => null,
            'roles' => array(),
            'locale' => 'en',
            'locales' => array('en'),
        ));

        $resolver->setRequired(array(
            'tags'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_page';
    }
}
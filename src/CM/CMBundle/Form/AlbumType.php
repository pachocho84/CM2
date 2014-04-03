<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlbumType extends BaseEntityType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('images', 'collection', array(
                'type' => new ImageEditType,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'options' => array(
                )
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Entity',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_imagealbum';
    }
}

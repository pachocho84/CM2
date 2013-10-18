<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;

class MultipleImagesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('images', 'collection', array(
                'label' => 'Add images',
                'required' => false,
                'type' => new ImageType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'widget_add_btn' => array(
                    'label' => 'add an image'
                ),
                'options' => array(
                    'horizontal' => true,
                    'label_render' => false,
                    'horizontal_input_wrapper_class' => "col-lg-8",
                )
        ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_image';
    }
}

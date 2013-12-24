<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Form\DataTransformer\ArrayCollectionToEntityTransformer;

class MultimediaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('link');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Multimedia',
            'groups' => array('Multimedia', 'Default'),
        	'em' => null,
            'roles' => array(),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_multimedia';
    }
}

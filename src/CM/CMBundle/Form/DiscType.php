<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Form\DataTransformer\DateTimeToYearTransformer;

class DiscType extends EntityType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    
        $builder->add('label')
            ->add($builder->create('date', 'number', array('label' => 'Year', 'error_bubbling' => false))->addModelTransformer(new DateTimeToYearTransformer))
            ->add('discTracks', 'collection', array(
                'type' => new DiscTrackType,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
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
            'data_class' => 'CM\CMBundle\Entity\Disc',
            'groups' => array('Disc', 'Default'),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_disc';
    }
}

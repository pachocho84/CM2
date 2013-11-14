<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Form\DataTransformer\ArrayCollectionToEntityTransformer;

class EventType extends EntityType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    
        $builder->add('eventDates', 'collection', array(
                'type' => new EventDateType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options' => array(
                )
            ))
            ->add($builder->create('posts', new PostType, array('label' => 'Post'))->addModelTransformer(new ArrayCollectionToEntityTransformer($options['em'])));
            // ->add('posts', 'collection', array(
            //     'type' => new PostType(),
            //     'by_reference' => false,
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'options' => array(
            //     )
            // ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Event',
            'groups' => array('event', 'Default'),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_event';
    }
}

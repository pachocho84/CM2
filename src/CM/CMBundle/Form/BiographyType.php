<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Form\DataTransformer\ArrayCollectionToEntityTransformer;

class BiographyType extends EntityType
{
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
                        'text' => array()
                    )
                ));
        } else {
            $builder->add(
                $builder->create('translations', new EntityTranslationType, array('label' => 'Body', 'error_bubbling' => false, 'title' => false))
                    ->addModelTransformer(new ArrayCollectionToEntityTransformer($options['em'], 'en'))
            );
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Biography'
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

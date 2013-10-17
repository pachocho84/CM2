<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\UserTagRepository;
use CM\CMBundle\Form\DataTransformer\UserTagNameToIdTransformer;

class EntityUserType extends AbstractType
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
        $builder
            ->add('user')
            ->add('admin')
            ->add('status', 'choice', array(
                'choices' => array(
                    EntityUser::STATUS_PENDING => 'Pending',
                    EntityUser::STATUS_ACTIVE => 'Active',
                    EntityUser::STATUS_REQUESTED => 'Requested',
                    EntityUser::STATUS_REFUSED_ADMIN => 'Refused by an admin',
                    EntityUser::STATUS_REFUSED_ENTITY_USER => 'Refused by an entity user',
                    EntityUser::STATUS_FOLLOWING => 'Following'
                )
            ))
            ->add('userTags', 'entity', array(
                'class' => 'CMBundle:UserTag',
                'query_builder' => function(UserTagRepository $em) use ($options) {
                    return $em->filterUserTags($options);
                },
                'multiple' => true,
                'by_reference' => false
            ))
            ->add('notification');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'em' => null,
            'locale' => 'en',
            'locales' => array('en'),
            'data_class' => 'CM\CMBundle\Entity\EntityUser'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_entityuser';
    }
}

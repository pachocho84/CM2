<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CM\CMBundle\Form\DataTransformer\UserToIntTransformer;
use CM\CMBundle\Form\DataTransformer\TagsToTextTransformer;
use CM\CMBundle\Entity\PageUser;

class PageUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    
        $builder->add($builder->create('user', 'hidden')->addModelTransformer(new UserToIntTransformer($options['em'])))
            ->add($builder->create('pageUserTags', 'hidden', array(
                    'attr' => array('tags' => array_reduce($options['tags'], function($carry, $a) { return $carry.(is_null($carry) ? '' : ';').$a->getId().','.$a; })),
                    'label' => 'Roles'
                ))->addModelTransformer(new TagsToTextTransformer($options['tags'], 'CM\CMBundle\Entity\PageUserTag')
            ))->add('admin', 'checkbox', array(
                'required' => false,
                'label' => 'Make admin'
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data instanceof PageUser && $data->getStatus() == PageUser::STATUS_REQUESTED) {
                $form->add('status', 'choice', array(
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => array(
                        PageUser::STATUS_REQUESTED => 'requested',
                        PageUser::STATUS_ACTIVE => 'accept',
                        PageUser::STATUS_REFUSED_ADMIN => 'refuse',
                    )
                ));
            }
         });
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {        
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\PageUser',
            'tags' => array(),
        ));

        $resolver->setRequired(array(
            'em',
            'tags'
        ));

        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_pageusertags';
    }
}

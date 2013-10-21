<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Group;
use CM\CMBundle\Entity\Page;
use CM\CMBundle\Entity\GroupRepository;
use CM\CMBundle\Entity\PageRepository;

class PostType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'choices' => array(
                    Post::TYPE_CREATION => 'Created'
                )
            ))
            ->add('creator')
            ->add('user');

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $form->add('group', 'entity', array(
                        'class' => 'CMBundle:Group',
                        'query_builder' => function (GroupRepository $er) use ($data) {
                            return $er->filterGroupsForUser($data->getUser());
                        },
                        'empty_value' => 'No group selected',
                        'attr' => array('class' => 'protagonists_group'),
                        'required' => false
                    ))
                    ->add('page', 'entity', array(
                        'class' => 'CMBundle:Page',
                        'query_builder' => function (PageRepository $er) use ($data) {
                            return $er->filterPagesForUser($data->getUser());
                        },
                        'empty_value' => 'No page selected',
                        'attr' => array('class' => 'protagonists_page'),
                        'required' => false
                    ));
            });
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Post'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_post';
    }
}

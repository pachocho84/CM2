<?php

namespace CM\CMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityType extends AbstractType
{
	protected $locales = array('en');

	public function __construct(array $locales)
	{
		$this->locales = $locales;
	}

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('translations', 'a2lix_translations', array(
    		'locales' => $this->locales,
		    'required' => false,
		    'fields' => array(
		        'slug' => array('display' => false)
		    )
		))->add('visible');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CM\CMBundle\Entity\Entity'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cm_cmbundle_entity';
    }
}

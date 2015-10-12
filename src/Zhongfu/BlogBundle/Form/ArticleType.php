<?php

namespace Zhongfu\BlogBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{

    public $aTypes;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('titre',  'text')
            ->add('slug',   'text')
            ->add('contenu', 'textarea', array('required' => false))
            ->add('type',   'entity', array(
                'class' => 'ZhongfuBlogBundle:Type',
                'property' => 'url',
                'choice_label' => 'name',
                ))
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zhongfu\BlogBundle\Entity\Article'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zhongfu_blogbundle_article';
    }
}

<?php

namespace Tabernicola\JukeCloudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tabernicola\JukeCloudBundle\Form\DataTransformer\TextToArtistTransformer;
use Tabernicola\JukeCloudBundle\Form\DataTransformer\TextToDiskTransformer;

class SongType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $diskTransformer=new TextToDiskTransformer($em);
        $artistTransformer=new TextToArtistTransformer($em);
        $builder
            ->add('title')
            ->add('number')
            ->add($builder->create('disk','text')->addModelTransformer($diskTransformer))
            ->add($builder->create('artist','text')->addModelTransformer($artistTransformer))
            ->add('files')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tabernicola\JukeCloudBundle\Entity\Song',
            'csrf_protection' => false,
        ))
        ->setRequired(array('em',))
        ->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}

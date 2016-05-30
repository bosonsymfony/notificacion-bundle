<?php

namespace UCI\Boson\NotificacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CorreoEntityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo', 'text')
            ->add('contenido', 'text')
            ->add('adjunto','file')
            ->add('user','entity',array(
                'multiple'=> true,
                'required'=> false,
                'class'=>'UCI\Boson\SeguridadBundle\Entity\Usuario'
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UCI\Boson\NotificacionBundle\Entity\Correo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'notificacionbundle_notificacionmail';
    }
}

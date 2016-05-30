<?php

namespace UCI\Boson\NotificacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TiempoRealServiceType extends AbstractType
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
            ->add('users','entity',array(
                'multiple'=> true,
                'class'=>'UCI\Boson\SeguridadBundle\Entity\Usuario'
            ))->add('roles','entity',array(
                'multiple'=> true,
                'class'=>'UCI\Boson\SeguridadBundle\Entity\Rol'
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UCI\Boson\NotificacionBundle\Form\Model\SendNotTiempoReal'
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

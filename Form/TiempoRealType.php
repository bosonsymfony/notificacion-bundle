<?php

namespace UCI\Boson\NotificacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TiempoRealType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipo', 'integer')
            ->add('titulo', 'text')
            ->add('contenido', 'text')
            ->add('user',null,array(
                'multiple'=> false,
                'expanded'=>false,
                'choices_as_values' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UCI\Boson\NotificacionBundle\Entity\TiempoReal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'uci_boson_notificacionbundle_tiemporeal';
    }
}

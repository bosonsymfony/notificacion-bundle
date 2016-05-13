<?php
/**
 * Created by PhpStorm.
 * User: alainfd
 * Date: 3/05/16
 * Time: 14:10
 */

namespace UCI\Boson\NotificacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class NotificacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', 'date')
            ->add('tipo', 'integer')
            ->add('titulo', 'string')
            ->add('contenido', 'string')
            ->add('autor', 'int');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'=>'UCI\Boson\NotificacionBundle\Entity\Notificacion'
        ));
    }

    public function getName()
    {
        // TODO: Implement getName() method.
    }

}
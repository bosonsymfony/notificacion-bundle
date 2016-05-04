<?php

namespace UCI\Boson\NotificacionBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10/03/16
 * Time: 9:56
 */
class NotificationCorreoService
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $manager;

    private $token;

    /**
     * NotificationService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->manager = $container->get("doctrine")->getManager();
        $this->token = $container->get('security.token_storage');
    }


    public function notifyByUser(){

    }

    public function modNotif($data){
        die('asdasd');
        print_r($data);die("\nFile:".__FILE__."\nLine:".__LINE__);
    }
}
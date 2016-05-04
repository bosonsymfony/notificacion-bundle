<?php

namespace UCI\Boson\NotificacionBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use UCI\Boson\NotificacionBundle\Entity\TiempoReal;

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10/03/16
 * Time: 9:56
 */
class NotificationTRService
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

    public function notifyByUser($tipo,$titulo,$contenido,$user){

        $secInfo = $this->container->get('notificacion.notification')->getUserSecurityInfo();
        $fecha = new \DateTime('yyyy-mm-dd');
        $notif = new TiempoReal();
//        $notif->setAutor()

    }
}
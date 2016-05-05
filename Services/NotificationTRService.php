<?php

namespace UCI\Boson\NotificacionBundle\Services;

use GuzzleHttp\Client;
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

    public function notifyByUser($titulo,$contenido ="",$user){
        $url = $this->container->getParameter('notification');
        $securityInf = $this->container->get("notificacion.notification")->getUserSecurityInfo();
        $client = new Client();
        $notif_data = array('user' => $user, 'mensaje' => $titulo);
        if($contenido !== ""){
            $notif_data['mensaje'] = $notif_data['mensaje']."\n".$contenido;
        }
        $body = array_merge(array('security_data' => $securityInf['data']),
            array('notif_data' => $notif_data ));

        $resp = $client->post($url,
            ['json' => $body,
                'headers' =>
                    [
                        'authorization' => $securityInf['token'],
                        'content-type' => 'application/json'
                    ]]
        );
        return $resp;

    }
}
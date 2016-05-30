<?php

namespace UCI\Boson\NotificacionBundle\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UCI\Boson\NotificacionBundle\Entity\TiempoReal;
use UCI\Boson\NotificacionBundle\Form\Model\SendNotTiempoReal;

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

    public function sendNotification(SendNotTiempoReal $entity)
    {
        $arrayNotifUsers = $this->manager->getRepository('NotificacionBundle:TiempoReal')->persistFormNotification($entity);
        /* notifico con el servicio */
        if (count($arrayNotifUsers) === 0)
            return false;
        else if (count($arrayNotifUsers) === 1)
            return $this->notifyByUser($entity->getTitulo(), $entity->getContenido(), $arrayNotifUsers[0]);
        else
            return $this->notifyByUsers($entity->getTitulo(), $entity->getContenido(), $arrayNotifUsers);
    }

    /**
     * Notifica a un usuario
     * @param $titulo
     * @param string $contenido
     * @param $user
     * @return \GuzzleHttp\Message\FutureResponse|
     * \GuzzleHttp\Message\ResponseInterface|
     * \GuzzleHttp\Ring\Future\FutureInterface|
     * null
     */
    public function notifyByUser($titulo, $contenido = "", $user)
    {

        $url = $this->container->getParameter('notification_url_server');
        $securityInf = $this->container->get("notificacion.notification")->getUserSecurityInfo();
        $client = new Client();
        $notif_data = array('user' => $user, 'mensaje' => $titulo);
        if ($contenido !== "") {
            $notif_data['mensaje'] = $notif_data['mensaje'] . "\n" . $contenido;
        }
        $body = array_merge(array('security_data' => $securityInf['data']),
            array('notif_data' => $notif_data));

        try{$resp = $client->post($url,
            ['json' => $body,
                'headers' =>
                    [
                        'authorization' => $securityInf['token'],
                        'content-type' => 'application/json'
                    ]]
        );
        }
        catch (ConnectException $exc){
            return false;
        }
        return $resp;
    }

    /**
     * @param $titulo
     * @param string $contenido
     * @param array $users
     * @return \GuzzleHttp\Message\FutureResponse|
     * \GuzzleHttp\Message\ResponseInterface|
     * \GuzzleHttp\Ring\Future\FutureInterface|
     * null
     */
    public function notifyByUsers($titulo, $contenido = "", array $users)
    {
        return $this->notifyByUser($titulo, $contenido, $users);
    }
}
<?php

namespace UCI\Boson\NotificacionBundle\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UCI\Boson\NotificacionBundle\Entity\TiempoReal;
use UCI\Boson\NotificacionBundle\Exception\NotificacionNotAuthenticationData;
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

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
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

    /**
     * @param SendNotTiempoReal $entity
     * @return bool|\GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     * @throws NotAuthenticationData
     */
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
     * @return bool|\GuzzleHttp\Stream\StreamInterface|null
     * @throws NotAuthenticationData
     */
    public function notifyByUser($titulo, $contenido = "", $user)
    {
        $url = $this->container->getParameter('notification_url_server');
        $securityInf = $this->container->get("notificacion.notification")->getUserSecurityInfo();
        if ($securityInf === false) {
            throw new NotAuthenticationData();
        }
        $client = new Client();
        $notif_data = array('user' => $user, 'titulo' => $titulo, 'mensaje' => $contenido);
        $body = array_merge(array('security_data' => $securityInf['data']), array('notif_data' => $notif_data));
        try {
            $resp = $client->post($url,
                ['json' => $body,
                    'headers' =>
                        [
                            'authorization' => $securityInf['token'],
                            'content-type' => 'application/json'
                        ]]
            )->json();
        } catch (ConnectException $exc) {
            return false;
        }
       return $resp;
//        $result = $resp->then(
//            function (ResponseInterface $res) {
//                return $res->getBody();
//
//        },
//            function (RequestException $e) {
//                return false;
//            });
       // dump($resp->getBody());
    }

    /**
     * Notifica a aun conjunto de usuarios obtenidos como parámetro
     * @param $titulo
     * @param string $contenido
     * @param array $users
     * @return bool|\GuzzleHttp\Stream\StreamInterface|null
     * @throws NotAuthenticationData
     */
    public function notifyByUsers($titulo, $contenido = "", array $users)
    {
        return $this->notifyByUser($titulo, $contenido, $users);
    }
}
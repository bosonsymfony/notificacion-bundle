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

    public function getUserSecurityInfo()
    {
        $token = $this->token->getToken();
        $roles = array();
        foreach ($token->getRoles() as $roleObj) {
            $roles[] = $roleObj->getRole();
        }
        $user = $this->manager->getRepository("SeguridadBundle:Usuario")->findOneByUsername($token->getUsername());
        if($user == null){
            return false;
        }
        $data = array('user' => $token->getUsername(),
            'userid' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $roles);

        return $this->prepareTokenInfo($data);
    }

    private function prepareTokenInfo(array $data, $keyDir = null)
    {
        if ($keyDir == null) {
            $keyDir = __DIR__ . DIRECTORY_SEPARATOR .
                ".." . DIRECTORY_SEPARATOR .
                "Resources" . DIRECTORY_SEPARATOR .
                "keys" . DIRECTORY_SEPARATOR .
                "BOSON.NOTIF.PRIVADA.pem";
        }
        $info = json_encode($data);
        $hash = md5($info);
        $pkeyid = openssl_pkey_get_private(file_get_contents($keyDir));

        //create signature
        openssl_sign($hash, $signature, $pkeyid);
        $exit = array("data" => $data, "token" => base64_encode($signature));


        return $exit;
    }

    public function notifyByUser(){

    }

    public function modNotif($data){
        die('asdasd');
        print_r($data);die("\nFile:".__FILE__."\nLine:".__LINE__);
    }
}
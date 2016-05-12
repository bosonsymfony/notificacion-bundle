<?php

namespace UCI\Boson\NotificacionBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    private $store_attachements;
    /**
     * NotificationService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container,$store_attachments)
    {
        $this->container = $container;
        $this->manager = $container->get("doctrine")->getManager();
        $this->token = $container->get('security.token_storage');
        $this->store_attachements = $store_attachments;
    }


    public function notifyByUser($titulo,$contenido,$usuarios,UploadedFile $adjunto= null){

        //Aqui notificamos
        try{
            $sender = $this->container->get('mailer');
            $mensaje = \Swift_Message:: newInstance()
                ->setSubject($titulo )
                ->setFrom("dacasals@uci.cu")
                ->setTo($usuarios)
                ->setBody($contenido);
            if($adjunto)
                $mensaje->attach(\Swift_Attachment::fromPath($adjunto->getRealPath()));

            return $sender->send($mensaje);
        }
        catch (\Exception $e)
        {
            $this->container->get('logger')->addCritical($e->getMessage());
        }

    }

    public function storeAdjunto(UploadedFile $file, $url = null){
        if(!$url)
            $url = $this->store_attachements;
        if(is_dir($url)){
            $file->move($url);
            return true;
        }
        return false;
    }
}
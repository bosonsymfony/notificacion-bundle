<?php

namespace UCI\Boson\NotificacionBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use UCI\Boson\NotificacionBundle\Form\Model\SendNotMail;

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

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private $token;
    /**
     * @var
     */
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

    /**
     * Crea y lanza una o notificación para un conjunto de roles o usuarios.
     * @param SendNotMail $entity
     * @return bool
     */
    public function sendNotification(SendNotMail $entity){
        /*llamo al registro de notificaciones en el repositorio*/
        $ResponsePersist = $this->manager->getRepository('NotificacionBundle:Correo')->persistFormNotification($entity);
        $arrayNotifUsers = $ResponsePersist['users'];
        if (count($arrayNotifUsers) > 0) {
            if ($entity->getAdjunto() instanceof UploadedFile) {
                $resp = $this->notifyByUser($entity->getTitulo(),
                    $entity->getContenido(), $arrayNotifUsers, $entity->getAdjunto());
                $this->storeAdjunto($entity->getAdjunto(),$ResponsePersist['id']);
            } else {
                $resp = $this->notifyByUser($entity->getTitulo(),
                $entity->getContenido(), $arrayNotifUsers);
            }
            return $resp ;
        }
        else{
            return false;
        }
    }
    /**
     * @param $titulo
     * @param $contenido
     * @param $usuarios
     * @param UploadedFile|null $adjunto
     * @return bool|int
     */
    public function notifyByUser($titulo, $contenido, $usuarios, UploadedFile $adjunto= null){

        //Aqui notificamos
        try{
            $sender = $this->container->get('mailer');
            $mensaje = \Swift_Message:: newInstance()
                ->setSubject($titulo )
                ->setFrom( $this->container->getParameter('mailer_user'))
                ->setTo($usuarios)
                ->setBody($contenido);
            if($adjunto)
                $mensaje->attach(\Swift_Attachment::fromPath($adjunto->getRealPath()));

           return $sender->send($mensaje);
        }
        catch (\Exception $e)
        {
            $this->container->get('logger')->addCritical($e->getMessage());
            return false;
        }
    }

    /**
     * Almacena en el servidor el
     * @param UploadedFile $file
     * @param null $id
     * @return bool
     */
    public function storeAdjunto(UploadedFile $file, $id){
        $url = $this->container->getParameter('notification_store_attachments');
        if($url){
            $zip = new \ZipArchive();
            $archive = $url.DIRECTORY_SEPARATOR.$id;
            $zip->open($archive, \ZipArchive::CREATE);
            $zip->addFromString($file->getClientOriginalName().".".$file->getClientOriginalExtension(), file_get_contents($file->getRealPath()));
            $zip->close();
        }
    }

    /**
     * Obtiene la url del adjunto
     * @param $id
     * @return bool|string
     */
    public function getNombreAdjunto($id){
        $url = $this->container->getParameter('notification_store_attachments');
        if($url){
            $zip = new \ZipArchive();
            $archive = $url.DIRECTORY_SEPARATOR.$id;
            $resp = $zip->open($archive);
            if($resp === true){
                $stat = $zip->statIndex(0);
                $zip->close();
                return basename(basename( $stat['name'] ));
            }

        }
        return false;
    }
}
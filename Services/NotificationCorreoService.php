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
        if(!is_array($ResponsePersist) || !array_key_exists('users',$ResponsePersist)){
            return false;
        }
        $arrayNotifUsers = $ResponsePersist['users'];

        if (count($arrayNotifUsers) > 0) {
            if ($entity->getAdjunto() instanceof UploadedFile) {
                $atachmentStored = $this->storeAdjunto($entity->getAdjunto(),$ResponsePersist['id']);
                $resp = $this->notifyByUser($entity->getTitulo(),
                    $entity->getContenido(), $arrayNotifUsers, $atachmentStored);
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
     * @param string|null $adjunto
     * @return bool|int
     */
    public function notifyByUser($titulo, $contenido, $usuarios, $adjunto= null){

        //Aqui notificamos
        try{
            $sender = $this->container->get('mailer');
            $mensaje = \Swift_Message:: newInstance()
                ->setSubject($titulo )
                ->setFrom( $this->container->getParameter('mailer_user'))
                ->setTo($usuarios)
                ->setBody($contenido);
            if($adjunto)
                $mensaje->attach(\Swift_Attachment::fromPath($adjunto));

           return $sender->send($mensaje);
        }
        catch (\Exception $e)
        {
            $this->container->get('logger')->addCritical($e->getMessage());
            return false;
        }
    }

    /**
     * Almacena en el servidor el adjunto recibido
     * @param UploadedFile $file
     * @param null $id
     * @return string | null
     */
    public function storeAdjunto(UploadedFile $file, $id){
        $url = $this->container->getParameter('notification_store_attachments');
        if($url){
            $zip = new \ZipArchive();
            $archive = $url.DIRECTORY_SEPARATOR.$id.'.zip';
            $zip->open($archive, \ZipArchive::CREATE);
            $zip->addFromString($file->getClientOriginalName(), file_get_contents($file->getRealPath()));
            $zip->close();
            return $archive;
        }
        return null;
    }

    /**
     * Elimina en el servidor el adjunto
     * @param UploadedFile $file
     * @param null $id
     * @return bool
     */
    public function deleteAdjunto($id){
        $url = $this->container->getParameter('notification_store_attachments');
        $resp = false;
        if(file_exists($url.DIRECTORY_SEPARATOR.$id)){
           $resp =  unlink($url.DIRECTORY_SEPARATOR.$id);
        }
        return $resp;
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
            $archive = $url.DIRECTORY_SEPARATOR.$id.'.zip';
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
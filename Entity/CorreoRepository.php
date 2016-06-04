<?php

namespace UCI\Boson\NotificacionBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use UCI\Boson\NotificacionBundle\Exception\NotificacionNotUserValid;
use UCI\Boson\NotificacionBundle\Form\Model\SendNotMail;

/**
 * CorreoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CorreoRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Persiste un conjunto de notificaciones dada la información que se obtiene del formulario
     *
     * @param SendNotTiempoReal $object
     * @return array Retorna el listado de usuarios a los que se le registró una notificación.
     */
    public function persistFormNotification(SendNotMail $object){
        try
        {
            $notified_users = $this->getListUsersToNotify($object);
            $entity = $this->createEntity($object->getTitulo(),$object->getContenido(),$object->getAutor(),$object->getUsers(),$object->getAdjunto());
            $resp = $this->persistNotification($entity);
            if(is_nan($resp) === true)
                return $resp;
            return array('users'=>$notified_users['email'], 'id'=>$resp);
        }
        catch(\Exception $ex){
            return $ex->getMessage();
        }
    }
    public function getListUsersToNotify(SendNotMail $object){
        $notified_users = array('id'=>array(),'email'=>array());
        $users = $object->getUsers();
        $roles = $object->getRoles();
        if(!$users instanceof ArrayCollection){
            throw new \Exception("Must has an ArrayCollection of Users");
        }
        foreach ($users as $user) {
            $notified_users['email'][] = $user->getEmail();
            $notified_users['id'][] = $user->getId();
        }
        if(!$roles instanceof ArrayCollection){
            throw new \Exception("Must has an ArrayCollection of Roles");
        }
        foreach ($roles as $role) {
            $usersByRole = $role->getUsuarios();
            foreach ($usersByRole as $item) {
                if(!in_array($item->getEmail(),$notified_users['email'])){
                    $users[] = $item;
                    $notified_users['email'][] = $item->getEmail();
                    $notified_users['id'][] = $item->getId();
                }
            }
        }
        return $notified_users;
    }


    public function persistNotification(Correo $entity){
        try
        {$this->_em->persist($entity);
            $this->_em->flush($entity);
            return  $entity->getId();
        }
        catch(\Exception $ex){
            return $ex->getMessage();
        }
    }
    public function updateEntity($titulo,$contenido,ArrayCollection $users,$adjunto,Correo $entity){
        $entity->setTitulo($titulo);
        $entity->setContenido($contenido);
        $new  = new ArrayCollection(array_merge($entity->getUser()->toArray(),$users->toArray()));
        if($adjunto instanceof UploadedFile){
            $entity->setAdjunto(true);
        }else{
            $entity->setAdjunto(false);
        }
        $entity->setFecha(new \DateTime());
        return $entity;
    }

    private function createEntity($titulo,$contenido,$autor,$users,$adjunto){
        $entity = new Correo();
        $tipo =$this->_em->getRepository('NotificacionBundle:TipoNotificacion')->findOneByNombre('Correo');
        $entity->setTitulo($titulo);
        $entity->setTipo($tipo);
        $entity->setContenido($contenido);
        $entity->setAutor($autor);
        foreach ($users as $user) {
            $entity->addUser($user);
        }
        if($adjunto instanceof UploadedFile){
            $entity->setAdjunto(true);
        }else{
            $entity->setAdjunto(false);
        }
        $entity->setFecha(new \DateTime());
        return $entity;
    }

    public function findClear($id)
    {
         $qb = $this->_em->createQueryBuilder();
        $qb->select('correo')
            ->from('NotificacionBundle:Correo','correo')
            ->where('correo.id = :identifier')
            ->setParameter('identifier',$id);
        return $entity = $qb->getQuery()->getOneOrNullResult();
    }
}

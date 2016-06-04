<?php

namespace UCI\Boson\NotificacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * TiempoReal
 *
 * @ORM\Table(name="not_tiempo_real")
 * @ORM\Entity(repositoryClass="UCI\Boson\NotificacionBundle\Entity\TiempoRealRepository")
 */
class TiempoReal
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean")
     */
    private $estado;


    /**
     * @ORM\ManyToOne(targetEntity="UCI\Boson\SeguridadBundle\Entity\Usuario")
     *  @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="UCI\Boson\NotificacionBundle\Entity\Notificacion", inversedBy="tiempoReales")
     *  @ORM\JoinColumn(name="notificacion_id", referencedColumnName="id")
     */
    private $notificacion;

    /**
     * Set estado
     *
     * @param boolean $estado
     *
     * @return TiempoReal
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return boolean
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set user
     *
     * @param \UCI\Boson\SeguridadBundle\Entity\Usuario $user
     *
     * @return TiempoReal
     */
    public function setUser(\UCI\Boson\SeguridadBundle\Entity\Usuario $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UCI\Boson\SeguridadBundle\Entity\Usuario
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get users
     *
     * @return \UCI\Boson\SeguridadBundle\Entity\Usuario
     */
    public function getUsers()
    {
        return array($this->user);
    }




    /**
     * Set notificacion
     *
     * @param \UCI\Boson\NotificacionBundle\Entity\Notificacion $notificacion
     *
     * @return TiempoReal
     */
    public function setNotificacion(\UCI\Boson\NotificacionBundle\Entity\Notificacion $notificacion = null)
    {
        $this->notificacion = $notificacion;

        return $this;
    }

    /**
     * Get notificacion
     *
     * @return \UCI\Boson\NotificacionBundle\Entity\Notificacion
     */
    public function getNotificacion()
    {
        return $this->notificacion;
    }
}

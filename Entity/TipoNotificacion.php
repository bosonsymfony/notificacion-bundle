<?php

namespace UCI\Boson\NotificacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoNotificacion
 *
 * @ORM\Table(name = "nom_tipo_notificacion")
 * @ORM\Entity(repositoryClass="UCI\Boson\NotificacionBundle\Entity\TipoNotificacionRepository")
 */
class TipoNotificacion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity = "Notificacion", mappedBy = "tipo")
     */
    private $notificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return NombreNotificacion
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set notificacion
     *
     * @param \UCI\Boson\NotificacionBundle\Entity\Notificacion $notificacion
     *
     * @return TipoNotificacion
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

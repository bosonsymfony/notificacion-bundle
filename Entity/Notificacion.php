<?php

namespace UCI\Boson\NotificacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UCI\Boson\BackendBundle\Validator\Constraints\Format;

/**
 * Notificacion
 *
 * @ORM\Table(name = "not_notificacion")
 * @ORM\Entity(repositoryClass="UCI\Boson\NotificacionBundle\Entity\NotificacionRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminante", type="string")
 * @ORM\DiscriminatorMap({"c" = "Correo", "e" = "Evento", "t" = "TiempoReal","n"="Notificacion"})
 */
class Notificacion
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     *
     * @ORM\ManyToOne(targetEntity = "UCI\Boson\NotificacionBundle\Entity\TipoNotificacion", inversedBy = "notificacion")
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="contenido", type="text")
     */
    private $contenido;

    /**
     * @var datetime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deleted_at;
    /**
     * @ORM\ManyToOne(targetEntity="UCI\Boson\SeguridadBundle\Entity\Usuario", inversedBy = "notificaciones")
     * @ORM\JoinColumn(name="autor_id", referencedColumnName="id")
     */
    private $autor;

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Notificacion
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }



    /**
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Notificacion
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set contenido
     *
     * @param string $contenido
     *
     * @return Notificacion
     */
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * Get contenido
     *
     * @return string
     */
    public function getContenido()
    {
        return $this->contenido;
    }

    /**
     * Set tipo
     *
     * @param \UCI\Boson\NotificacionBundle\Entity\TipoNotificacion $tipo
     *
     * @return Notificacion
     */
    public function setTipo(\UCI\Boson\NotificacionBundle\Entity\TipoNotificacion $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \UCI\Boson\NotificacionBundle\Entity\TipoNotificacion
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set autor
     *
     * @param \UCI\Boson\SeguridadBundle\Entity\Usuario $autor
     *
     * @return Notificacion
     */
    public function setAutor(\UCI\Boson\SeguridadBundle\Entity\Usuario $autor = null)
    {
        $this->autor = $autor;

        return $this;
    }

    /**
     * Get autor
     *
     * @return \UCI\Boson\SeguridadBundle\Entity\Usuario
     */
    public function getAutor()
    {
        return $this->autor;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Notificacion
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }
}

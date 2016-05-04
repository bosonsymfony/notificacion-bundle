<?php

namespace UCI\Boson\NotificacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TiempoReal
 *
 * @ORM\Table(name="not_tiempo_real")
 * @ORM\Entity(repositoryClass="UCI\Boson\NotificacionBundle\Entity\TiempoRealRepository")
 */
class TiempoReal extends Notificacion
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
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean")
     */
    private $estado;


    /**
     * @ORM\OneToOne(targetEntity="UCI\Boson\SeguridadBundle\Entity\Usuario")
     */
    private $user;


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
}

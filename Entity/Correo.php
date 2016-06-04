<?php

namespace UCI\Boson\NotificacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Correo
 *
 * @ORM\Table(name="not_correo")
 * @ORM\Entity(repositoryClass="UCI\Boson\NotificacionBundle\Entity\CorreoRepository")
 */
class Correo extends Notificacion
{


    /**
     * @var boolean
     *
     * @ORM\Column(name="adjunto", type="boolean")
     */
    private $adjunto;

    /**
     * @ORM\ManyToMany(targetEntity="UCI\Boson\SeguridadBundle\Entity\Usuario")
     * @ORM\JoinTable(name="not_correo_usuario")
     */
    private $user;


    /**
     * Set adjunto
     *
     * @param boolean $adjunto
     *
     * @return Correo
     */
    public function setAdjunto($adjunto)
    {
        $this->adjunto = $adjunto;

        return $this;
    }

    /**
     * Get adjunto
     *
     * @return boolean
     */
    public function getAdjunto()
    {
        return $this->adjunto;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add user
     *
     * @param \UCI\Boson\SeguridadBundle\Entity\Usuario $user
     *
     * @return Correo
     */
    public function addUser(\UCI\Boson\SeguridadBundle\Entity\Usuario $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \UCI\Boson\SeguridadBundle\Entity\Usuario $user
     */
    public function removeUser(\UCI\Boson\SeguridadBundle\Entity\Usuario $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUser()
    {
        return $this->user;
    }


    public function getId()
    {
        return parent::getId();
    }
}

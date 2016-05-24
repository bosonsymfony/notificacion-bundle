<?php

namespace UCI\Boson\NotificacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Evento
 *
 * @ORM\Table(name="not_evento")
 * @ORM\Entity(repositoryClass="UCI\Boson\NotificacionBundle\Entity\EventoRepository")
 */
class Evento extends Notificacion
{
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;



    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Evento
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
}

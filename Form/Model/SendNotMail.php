<?php
namespace UCI\Boson\NotificacionBundle\Form\Model;
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 6/05/16
 * Time: 10:08
 */
use Symfony\Component\Validator\Constraints as Assert;

class SendNotMail
{

    /**
     * @Assert\NotNull()
     */
    private $roles;


    /**
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $users;


    private $autor;
    /**
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $contenido;

    /**
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $titulo;

    /**
     * @Assert\File()
     */
    private $adjunto;

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return mixed
     */
    public function getAdjunto()
    {
        return $this->adjunto;
    }

    /**
     * @param mixed $adjunto
     */
    public function setAdjunto($adjunto)
    {
        $this->adjunto = $adjunto;
    }

    /**
     * @return mixed
     */
    public function getContenido()
    {
        return $this->contenido;
    }

    /**
     * @return mixed
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    public static function getChoices()
    {
        return array('male', 'female');
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @param mixed $contenido
     */
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;
    }

    /**
     * @param mixed $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * @return mixed
     */
    public function getAutor()
    {
        return $this->autor;
    }

    /**
     * @param mixed $autor
     */
    public function setAutor($autor)
    {
        $this->autor = $autor;
    }
}
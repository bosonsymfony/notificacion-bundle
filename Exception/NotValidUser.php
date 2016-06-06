<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 5/06/16
 * Time: 17:54
 */

namespace UCI\Boson\NotificacionBundle\Exception;


use Exception;

class NotValidUser extends \Exception
{
    public function __construct($message ="You must provide a valid User to notificate. : null  given", $code = 0, Exception $previous= null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return $this->message;
    }


}
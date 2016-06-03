<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2/06/16
 * Time: 15:41
 */

namespace UCI\Boson\NotificacionBundle\Exception;


use Exception;

class NotAuthenticationData extends \Exception
{
    public function __construct($message = "You are not authenticated", $code = 401, Exception $previous= null)
    {
        parent::__construct($message, $code, $previous);
    }

}
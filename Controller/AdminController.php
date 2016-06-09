<?php

namespace UCI\Boson\NotificacionBundle\Controller;


use UCI\Boson\BackendBundle\Controller\BackendController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class AdminController
 * @package UCI\Boson\NotificacionBundle\Controller
 */
class AdminController extends BackendController
{
    /**
     * @Route(path="/notificacion/admin/scripts/config.notificacion.js", name="notificacion_app_config")
     */
    public function getAppAction()
    {
        return $this->jsResponse('NotificacionBundle:Scripts:config.js.twig');
    }
}

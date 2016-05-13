<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16/01/15
 * Time: 19:47
 */

namespace UCI\Boson\NotificacionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UCI\Boson\NotificacionBundle\Entity\TipoNotificacion;

/**
 * Class LoadTipoNotifcation. Carga en la base de datos los tipos de notificaciones existentes..
 *
 * @author Daniel Arturo Casals Amat<dacasals@uci.cu>
 * @package UCI\Boson\TrazasBundle\DataFixtures\ORM
 */
class LoadTipoNotifcation implements FixtureInterface{
    /**
     * Carga en la tabla TipoNotificacion los tipos de notificaciones existentes. Si se añade algún otro se debe incluir en el arreglo $tipos.
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $tipos = array("Evento","Tiempo Real","Correo");
        foreach($tipos as $tipo){
            $tipoNew = new TipoNotificacion();
            $tipoNew->setNombre($tipo);
            $manager->persist($tipoNew);
            $manager->flush();
        }

    }


} 
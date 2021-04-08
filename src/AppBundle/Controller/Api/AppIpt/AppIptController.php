<?php

namespace AppBundle\Controller\Api\AppIpt;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

use AppBundle\Entity AS Entity;

class AppIptController extends FOSRestController
{
    /**
     * @Rest\Get("/api/appipt/app-asd.-sdfghjkl-app-tecnicos-lsosddd-isu-123/login")
     */
    public function loginAction(Request $request)
    {
        $usuario = '';
        $clave   = '';
        $em = $this->getDoctrine()->getManager();
        $response = array("resultado" => 0);

        if($request->query->has('usuario')==false || $request->query->has('clave')==false) {
            return $response;
        }

        $usuario = utf8_decode($request->query->get('usuario'));
        $clave   = utf8_decode($request->query->get('clave'));

        // encriptación de clave del sistema de ipt
        $lsAbc = str_split(utf8_decode('ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz1234567890!"·$%&/()=?¿*'));
        $lsKey = str_split(utf8_decode('hgFGJKLÑedcBNM0ñnmlkRTYU65yxw)(/&%4321zvutsrZXCV987qpojifba*¿?=$·"!QWEIOPASDH'));
        $claveIngresada = str_split($clave);
        $claveEncriptada = "";
        $indice = 0;

        for($i = 0; $i < count($claveIngresada); $i++) {
            $indice = array_search($claveIngresada[$i], $lsAbc);
            $claveEncriptada .= $lsKey[$indice];
        }

        $usuario = $em->getRepository(Entity\Usuario::class)->findOneBy(["username" => $usuario, "password" => $claveEncriptada]);

        if ($usuario) {

            $perfil = $em->getRepository(Entity\Perfil::class)->findOneBy(["id" => $usuario->getPerfil()->getNombre()]);

            $response = array(
              "resultado" => 1,
              "usuarioNombre" => $usuario->getNombre(),
              "usuarioId" => $usuario->getHash(),
              "usuario" => array(
                "nombre" => $usuario->getNombre(),
                "hash" => $usuario->getHash(),
                "perfil" => $usuario->getPerfil()->getNombre(),
                "appVersion" => $perfil && $perfil->getAppVersion()? $perfil->getAppVersion() : '0.0.0',
              )
            );
        }

        return $response;
    }

    /**
     * @Rest\Get("/api/appipt/app-asd.-sdfghjkl-app-tecnicos-lsosddd-isu-123/login-with-hash")
     */
    public function loginWithHashAction(Request $request)
    {
        $hash = '';
        $response = array("resultado" => 3);
        $em = $this->getDoctrine()->getManager();

        if($request->query->has('hash')==false) {
            return $response;
        }

        $hash = utf8_decode($request->query->get('hash'));

        $usuario = $em->getRepository(Entity\Usuario::class)->findOneBy(["hash" => $hash]);

        if ($usuario) {

            $perfil = $em->getRepository(Entity\Perfil::class)->findOneBy(["id" => $usuario->getPerfil()->getNombre()]);

            $response = array(
              "resultado" => 1,
              "usuario" => array(
                "nombre" => $usuario->getNombre(),
                "perfil" => $usuario->getPerfil()->getNombre(),
                "hash" => $usuario->getHash(),
                "appVersion" => $perfil && $perfil->getAppVersion()? $perfil->getAppVersion() : '0.0.0',
              )
            );
        }

        return $response;
    }


    /**
     *  @Rest\Post("/api/appipt/app-asd.-sdfghjkl-app-tecnicos-lsosddd-isu-123/actualizar-conexion-coordenadas")
     */
    public function actualizarConexionCoordenadasAction(Request $request)
    {
        $response = array("resultado" => 0);

        if($request->request->has('idH')==false || $request->request->has('idC')==false || $request->request->has('fe')==false
                || $request->request->has('la')==false || $request->request->has('lo')==false || $request->request->has('di')==false ) {

            return $response;
        }

        $id_hash  = $request->request->get('idH');
        $id_conexion = $request->request->get('idC');
        $latitud     = $request->request->get('la');
        $longitud    = $request->request->get('lo');
        $direccion   = utf8_decode($request->request->get('di'));
        $fechaHoraGeoreferencia = date_create_from_format('d/m/Y H:i:s',$request->request->get('fe'));

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $usuario = $em->getRepository(Entity\Usuario::class)->findOneBy(["hash" => $id_hash]);

        if($this->get('app.herramienta')->validarUsuario($usuario)) {

            $query= "update inmuebles set latitud='".$latitud."', longitud='".$longitud."', direccion_google='".$direccion."', id_usuario=".$usuario->getId().",app_ventana='APP_IPT' from inmuebles i inner join conexiones c on c.id_inmueble=i.id_inmueble where c.id_conexion=".$id_conexion.";";
            $res = $connection->executeUpdate($query);

            if ($res) {

                $ogc = $em->getRepository(Entity\OcaGeoreferenciaConexion::class)->findOneBy(["idConexion" => $id_conexion]);

                $ogcg = new Entity\OcaGeoreferenciaConexion\OcaGeoreferenciaConexionGeoreferencia();
                $ogcg->setIdConexion($id_conexion);
                $ogcg->setLatitud($latitud);
                $ogcg->setLongitud($longitud);
                $ogcg->setDireccion($direccion);
                $ogcg->setUsuario($usuario);
                $ogcg->setFechaHoraGeoreferencia($fechaHoraGeoreferencia);
                $ogcg->setFechaHoraSincronizado(new \DateTime());

                if ($ogc) {
                    $ogc->addGeoreferencia($ogcg);
                } else {
                    $em->persist($ogcg);
                }

                $em->flush();

            }

//            $query2= "insert into OcaGeoreferenciaConexionGeoreferencia (id_conexion, id_usuario, latitud, longitud, direccion, fechaHoraGeoreferencia, fechaHoraSincronizado) values ('".$id_conexion."','".$results[0]["id_usuario"]."','".$latitud."','".$longitud."','".$direccion."','".$fechaHoraGeoreferencia->format('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."');";
//            $res2 = $connection->executeUpdate($query2);

            $response = array("resultado" => 1);

        } else {
            $response = array("resultado" => 3);
        }

        return $response;
    }


    /**
     *  @Rest\Post("/api/appipt/app-asd.-sdfghjkl-app-tecnicos-lsosddd-isu-123/finalizar-tarea-elemento")
     */
    public function finalizarrTareaElementoAction(Request $request)
    {
        $response = array("resultado" => 0);

        if($request->request->has('tarea')==false || $request->request->has('tareaElemento')==false || $request->request->has('usuario')==false) {
            $response = array("resultado" => 0, "error" => "request no valida");
            return $response;
        }

        $tareaParam         = $request->request->get('tarea');
        $usuarioParam       = $request->request->get('usuario');
        $tareaElementoParam = $request->request->get('tareaElemento');

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $usuario = $em->getRepository(Entity\Usuario::class)->findOneBy(["hash" => $usuarioParam]);

        if($this->get('app.herramienta')->validarUsuario($usuario)) {

          $tareaElemento = $em->getRepository(Entity\Red\Obra\TareaElemento::class)->find($tareaElementoParam);

          if ($tareaElemento) {

            if ($tareaElemento->getTarea()->getTipo() == Entity\Red\Obra\Tarea::TIPO_FUSION
                  || $tareaElemento->getTarea()->getTipo() == Entity\Red\Obra\Tarea::TIPO_INSTALACION_FUSION) {

              $ne = new Entity\Red\Elemento\Estado(Entity\Red\Elemento\Estado::ESTADO_FUSIONADO);
              $ne->setCreadaPor($usuario);
              $ne->setElemento($tareaElemento->getElemento()->getElemento());
              $tareaElemento->getElemento()->getElemento()->setEstadoActual($ne);


              $em->persist($ne);
              $em->flush();

              // si el padre estaba iluminado tambièn se deberìa iluminar el elemento
              if ($tareaElemento->getElemento()->getElemento()->getParent() && $tareaElemento->getElemento()->getElemento()->getParent()->getEstadoActual()->getEstado() == Entity\Red\Elemento\Estado::ESTADO_ILUMINADO) {

                $ne = new Entity\Red\Elemento\Estado(Entity\Red\Elemento\Estado::ESTADO_ILUMINADO);
                $ne->setCreadaPor($usuario);
                $ne->setElemento($tareaElemento->getElemento()->getElemento());
                $tareaElemento->getElemento()->getElemento()->setEstadoActual($ne);

                $em->persist($ne);
                $em->flush();

              }

            } else {

              $ne = new Entity\Red\Elemento\Estado(Entity\Red\Elemento\Estado::ESTADO_INSTALADO);
              $ne->setCreadaPor($usuario);
              $ne->setElemento($tareaElemento->getElemento()->getElemento());
              $tareaElemento->getElemento()->getElemento()->setEstadoActual($ne);

              $em->persist($ne);
              $em->flush();

            }

            $tareaElemento->setFinalizadaPor($usuario);
            $tareaElemento->setFechaFinalizada(new \DateTime());
            $tareaElemento->setEstado(Entity\Red\Obra\TareaElemento::ESTADO_FINALIZADA);
            $em->flush();

            $response = array("resultado" => 1, "tarea" => $tareaElemento->getTarea()->getArray());

          } else {

            $response = array("resultado" => 2);
          }

        } else {

          $response = array("resultado" => 3);
        }

        return $response;
    }

    /**
     * @Rest\Get("/api/appipt/app-asd.-sdfghjkl-app-tecnicos-lsosddd-isu-123/obra/tareas")
     */
    public function obraTareasAction(Request $request)
    {
        $usuario = '';
        $tareasArray = [];
        $response = array("resultado" => 0);

        if($request->query->has('usuario')==false) {
            return $response;
        }

        $usuario = utf8_decode($request->query->get('usuario'));

        $query= "
            select
              cp.id_cuadrilla
            from usuarios u
            inner join personal p on p.usuario_id=u.id_usuario
            inner join cuadrillas_personal cp on cp.id_personal=p.id_personal
            where u.id_hash = :usuario
        ";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('usuario', $usuario);
        $statement->execute();
        $results = $statement->fetchAll();

        $response = array("resultado" => 0);
        $hoy = new \DateTime();


        if (count($results)>0) {

          foreach ($results as $r) {

            $tareas = $em->getRepository("AppBundle:Red\Obra\Tarea")->findBy(["cuadrilla" => $r['id_cuadrilla']]);

            if (count($tareas) > 0) {
              foreach($tareas as $tarea) {
                if ($tarea->getFecha() < $hoy && $tarea->getAvance() < 100) {
                  $tareasArray[] = $tarea->getArray();
                }
              }
            }

          }

          $response = array(
            "resultado" => 1,
            "tareas" => $tareasArray
          );
        }

        return $response;
    }
}
